@extends('layouts.page')

@section('title', 'Szemöldök | Alföldy Dóra')

@section('page')
    <x-sections.service-hero
        title="Szemöldök"
        category="Az arcod kerete"
        text="A szemöldök az egyik legfontosabb része az arcnak. Egy szép, jó ívű, színű és formájú szemöldök nagyban hozzájárul az arc kifejezőképességéhez."
        secondaryText="A szemöldök szálak növekedése, hossza, valamint az, hogy hova esik a szemöldökcsonton szintén befolyásoló tényező. Mindenkinek egyénre szabottan készítem el a számára előnyös ívű, formájú, színű szemöldököt úgy, hogy természetes hatású legyen."
        image="/images/content/FL-3-min.jpg"
        imageAlt="Szemöldök munka"
        width="w-[75%]"
        wideImage="true"
    />

    <div class="w-[93%] md:w-[75%] mx-auto my-12"><div class="divider"></div></div>

    <x-sections.content-card
        title="Szemöldök"
        text="Ahhoz, hogy szép és tartós szemöldököt tudjak Neked készítsek több tényezőt is figyelembe kell venni a kezelés előtt és után is."
        featuresTitle="Kezelés előtt:"
        :features="[
            'Érdemes a szemöldök festésre smink nélkül érkezni.',
            'A szemöldök festést megelőző 1 napban nem ajánlott olajos krémeket, szérumokat használni az arcon ugyanis gyengítheti a festés tartósságát.'
        ]"
        features2Title="Kezelés után:"
        :features2="[
            '24 óráig nem érheti víz, gőz, pára a szemöldököd',
            'Érdemes az olajos krémeket szemöldök festés után is kerülni 1 napig.',
            'Nem szabad dörzsölni a szemöldöködet kezelés után mert gyengítheti a festés hatását.'
        ]"
        image="/images/content/FL-1-min.jpg"
        width="w-[75%]"
    />

    <div class="w-[93%] md:w-[75%] mx-auto my-12"><div class="divider"></div></div>

    <section class="w-[93%] md:w-[75%] mx-auto grid grid-cols-1 md:grid-cols-2 gap-12 lg:gap-16 mb-24">
        <x-sections.pricing-card
            title="Szemöldök formázás és szemöldök festés"
            text="A szemöldök festés olyanoknak ajánlom, akik tartós és esztétikus szemöldökre vágynak. Ez a kezelés egy szemöldök szedésből, formázásból és szemöldök festésből áll. A festék bőrön bőrtípustól függően 3-5 napig tartós, a szemöldök szálakon pedig akár 3-4 hétig is."
            image="/images/content/image1-510x765.jpeg"
            imagePosition="object-[50%_84%]"
            :features="['tartós megoldást keresel szemöldököd festésére', 'nem szeretnél kitetováltatni a szemöldöködet', 'rendezett és esztétikus szemöldökre vágysz', 'időt szeretnél spórolni a mindennapokban']"
            priceDetails="<strong>Időtartam:</strong> 30 – 50 perc
            <strong>Ára:</strong>
            Szemöldök szedés és festés: 6 500 Ft
            Szemöldök szedés: 3 500 Ft
            Szemöldök festés: 4 000 Ft"
        />

        <x-sections.pricing-card
            title="Szemöldök laminálás"
            text="A szemöldök laminálás egy olyan kezelés, aminek célja, hogy a szemöldök szőrszálai rendezettebbek, szebb formájú és dúsabb hatásúak legyenek — úgy, hogy a természetes szőr irányát és állását tartósan átalakítja. Különösen hasznos ritkás, szétszórt vagy nehezen formázható szemöldök esetén."
            image="/images/content/IMG_2250-scaled-2048x2048-min.jpg"
            :features="['vastagabb dúsabb szemöldökre vágysz', 'optikailag dúsabb, szabályosabb szemöldököt szeretnél', 'rendezettebb szemöldökre vágysz', 'nem vagy elégedett a szemöldököd formájával', 'időt szeretnél spórolni a mindennapokban']"
            priceDetails="<strong>Időtartam:</strong> 45 perc – 1 óra
            <strong>Ára:</strong>
            Szemöldök laminálás: 7 500 Ft
            Szemöldök laminálás és szemöldök szedés: 8 500 Ft
            Szemöldök laminálás szemöldök szedéssel és festéssel: 9 500 Ft"
        />
    </section>

    <div class="w-[93%] md:w-[75%] mx-auto my-12"><div class="divider"></div></div>

    <x-sections.content-card
        title="Utókezelés"
        text="A szemöldök festés után fontos, hogy amennyiben lehetséges ne érje víz, gőz, hő 24 óráig a szemöldököt. A tartósság érdekében az egyik legfontosabb, hogy ne dörzsöld a szemöldököd mert annak hatására a festék megkophat. Sminklemosó nyugodtan érheti, de ügyelni kell ott is a finom mozdulatokra."
        image="/images/content/DSC_4461-min.jpg"
        width="w-[75%]"
    />

    <div class="w-[93%] md:w-[75%] mx-auto my-12"><div class="divider"></div></div>

    <x-sections.salon-info />
@endsection
