import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const root = path.resolve(__dirname, '..');

const imagesDir = path.join(root, 'public', 'images');
const variantsDir = path.join(imagesDir, 'variants');
const manifestPath = path.join(variantsDir, 'manifest.json');

const widths = [240, 320, 360, 480, 640, 720, 960, 1280, 1600, 1920];
const supportedExts = new Set(['.jpg', '.jpeg', '.png', '.webp']);
const force = process.argv.includes('--force');
const webpQuality = 78;
const avifQuality = 62;

let sharp;
try {
  sharp = (await import('sharp')).default;
} catch {
  console.error('Missing dependency: sharp. Run `npm install --save-dev sharp`.');
  process.exit(1);
}

function listFiles(dir) {
  const out = [];
  if (!fs.existsSync(dir)) return out;
  for (const entry of fs.readdirSync(dir, { withFileTypes: true })) {
    const full = path.join(dir, entry.name);
    if (entry.isDirectory()) {
      if (full === variantsDir) continue;
      out.push(...listFiles(full));
    } else {
      out.push(full);
    }
  }
  return out;
}

fs.mkdirSync(variantsDir, { recursive: true });

const files = listFiles(imagesDir).filter((file) => supportedExts.has(path.extname(file).toLowerCase()));
const manifest = {
  version: 2,
  generatedAt: new Date().toISOString(),
  images: {},
};

for (const file of files) {
  const ext = path.extname(file).toLowerCase();
  const base = path.basename(file, ext);
  // Get relative path from imagesDir and prepend 'images/'
  const relPath = path.relative(imagesDir, file);
  const relKey = path.join('images', relPath).replace(/\\/g, '/');

  try {
    const img = sharp(file);
    const meta = await img.metadata();
    if (!meta.width) continue;

    const entriesWebp = {};
    const entriesAvif = {};
    for (const w of widths) {
      if (w > meta.width) continue;
      
      // Use a flat name for variants to keep things simple, or preserve structure?
      // Flat name is safer for manifest lookups if keys are unique basenames, 
      // but relKey is better.
      // Let's use a hashed or safe filename for variants if they collide, 
      // but for now, base + width is fine if images are mostly flat.
      const outNameWebp = `${base}-${w}.webp`;
      const outNameAvif = `${base}-${w}.avif`;
      const outPathWebp = path.join(variantsDir, outNameWebp);
      const outPathAvif = path.join(variantsDir, outNameAvif);

      if (!force && fs.existsSync(outPathWebp)) {
        entriesWebp[w] = `/images/variants/${outNameWebp}`;
      } else {
        await img
          .clone()
          .resize({ width: w })
          .toFormat('webp', { quality: webpQuality })
          .toFile(outPathWebp);
        entriesWebp[w] = `/images/variants/${outNameWebp}`;
      }

      if (!force && fs.existsSync(outPathAvif)) {
        entriesAvif[w] = `/images/variants/${outNameAvif}`;
      } else {
        await img
          .clone()
          .resize({ width: w })
          .toFormat('avif', { quality: avifQuality })
          .toFile(outPathAvif);
        entriesAvif[w] = `/images/variants/${outNameAvif}`;
      }
    }

    if (Object.keys(entriesWebp).length || Object.keys(entriesAvif).length) {
      manifest.images[relKey] = {
        webp: entriesWebp,
        avif: entriesAvif,
      };
    }
  } catch (err) {
    console.error(`Error processing ${file}:`, err.message);
  }
}

fs.writeFileSync(manifestPath, JSON.stringify(manifest, null, 2));
console.log(`Generated variants for ${Object.keys(manifest.images).length} images.`);
