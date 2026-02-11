@extends('layouts.page')

@section('title', 'Szempilla | Alföldy Dóra')

@section('page')
    <x-sections.service-hero
        title="Szempilla <span class='text-brand-gold'>Styling</span>"
        category="Szempilla"
        text="A szempilla hosszabbítással szép és látványos hatást lehet elérni, amely kiemeli a tekinteted. Ez egy olyan technika, amivel nem csak dúsabb, hanem hosszabb szempillákat varázsolhatunk Neked."
        image="/images/content/DSC_7810-scaled-min.jpg"
        imageAlt="Szempilla munka"
        width="w-[75%]"
    />

    <div class="w-[93%] md:w-[75%] mx-auto my-12"><div class="divider"></div></div>

    <x-sections.content-card
        title="Szempilla építés"
        text="A műszempilla szettek személyre szabottan készülnek. Hosszát szempilláid hossza adja, ívét pedig a szemed és arcod formája, dússágát pedig úgy alakítjuk ahogy Neked a legjobb, legkényelmesebb. A szempillahosszabbítás során a saját szempillákra egyenként applikálunk műpillákat. A saját pilláid több méretből tevődnek össze, így a tökéletes forma elérése érdekében az applikált műpillák is.

        Kényelmed és komfortérzeted ugyanolyan fontos, mint az, hogy szempillád és sminked csodaszép legyen. Kényelmes kozmetikai ágyon pihenhetsz miközben szépülsz. A tökéletes és tartós végeredményt a szakmai tapasztalat mellett a minőségi eszközök és termékek garantálják."
        image="/images/content/sima-3-4D.--min.jpg"
        width="w-[75%]"
    />

    <div class="w-[93%] md:w-[75%] mx-auto my-12"><div class="divider"></div></div>

    <section class="w-[93%] md:w-[75%] mx-auto grid grid-cols-1 md:grid-cols-2 gap-12 lg:gap-16 mb-24">
        <x-sections.pricing-card
            title="ProMade 3-4D volume"
            text="Mint, ahogyan a nevében is benne van ez egy előre gyártott prémium műszempilla. Jelenleg ez a piac legújabb terméke. A kézzel készített volume szempillákkal való munka az építést nagyon hosszúra nyújtja. Egy vendég több órát tölt el nálam 3-4 hetente, amit szerettem volna egy picit lerövidíteni. A ProMade pillákkal rövidebb idő alatt, extrán tartós szetteket lehet készíteni."
            image="/images/content/IMG_1711_4-min.jpg"
            featuresTitle="Miben más a ProMade szempilla?"
            :features="['rövidebb időt vesz igénybe az építés', 'kisebb az esély a fan-ok összecsukódására', 'könnyebbek, tartósabbak']"
            priceDetails="<strong>Időtartam:</strong> Új szett 1.5 – 2 óra, töltés 1 – 1.5 óra
            <strong>Ára:</strong> Új szett 22 000 Ft
            <strong>Töltés:</strong> 0-14. napig 16 000 Ft, 15-21. napig 16 500 Ft, 22-28. napig 17 000 Ft

            29.naptól új szett készül."
        />

        <x-sections.pricing-card
            title="3-4D Volume"
            text="A 3-4D szettnél szintén a természetességre törekszünk a dúsabb, látványosabb hatás mellett. A 3-4D azt jelenti, hogy 1 saját szempillára 3/4 db műszempilla kerül fel. A műszempillák könnyed viseletként szolgálnak és nem nehezítik el a saját szempilla állományt. Munkám során előnyben részesítem a professzionális anyagokat és termékeket."
            image="/images/content/BAC1A83C-2B44-435E-8CA9-BEFB85DFFC65-2-min.jpg"
            :features="['hosszabb szempillákat szeretnél', 'dúsabb tekintetre vágysz', 'nem szeretnél többet a szempillaspirállal bíbelődni', 'időt szeretnél spórolni', 'természetes, mégis látványos eredmény']"
            priceDetails="<strong>Időtartam:</strong> Új szett 3 óra, töltés 2 – 2.5 óra
            <strong>Ára:</strong> Új szett 21 000 Ft
            <strong>Töltés:</strong> 0-14. napig 15 000 Ft, 15-21. napig 15 500 Ft, 22-28. napig 16 000 Ft

            29.naptól új szett készül."
        />

        <x-sections.pricing-card
            title="2D Duplázott"
            text="A duplázott szett is természetesség jegyében készül, de már egy fokkal dúsabb hatást lehet vele elérni mint az 1D-vel. A 2D azt jelenti, hogy 1 saját szempillára 2 db műszempilla kerül felhelyezésre. Olyanoknak javasolt, akik természetes eredményt szeretnének elérni."
            image="/images/content/IMG_7912-min.jpeg"
            :features="['időt szeretnél spórolni', 'természetes, enyhén látványos eredmény', 'hosszabb szempillákat szeretnél', 'enyhén dús szempillákat szeretnél']"
            priceDetails="<strong>Időtartam:</strong> Új szett 2 óra, töltés 1.5 – 2 óra
            <strong>Ára:</strong> Új szett 20 000 Ft
            <strong>Töltés:</strong> 0-14. napig 14 000 Ft, 15-21. napig 14 500 Ft, 22-28. napig 15 000 Ft

            29.naptól új szett készül."
        />

        <x-sections.pricing-card
            title="1D Classic"
            text="Az 1D szettnél első sorban a természetességre törekszünk. Az 1D azt jelenti, hogy 1 saját szempillára 1 db műszempilla kerül felhelyezésre. Ez első sorban olyanoknak ajánlott, akik visszafogott végeredményt és szempillaspirálozott hatást szeretnének elérni."
            image="/images/content/IMG_0771-min.jpg"
            :features="['természetes hatást szeretnél elérni', 'nem szeretnél többet a szempillaspirállal bíbelődni', 'időt szeretnél spórolni']"
            priceDetails="<strong>Időtartam:</strong> Új szett 2 óra, töltés 1-1.5 óra
            <strong>Ára:</strong> Új szett 19 000 Ft
            <strong>Töltés:</strong> 0-14. napig 13 000 Ft, 15-21. napig 13 500 Ft, 22-28. napig 14 000 Ft

            29.naptól új szett készül."
        />
    </section>

    <div class="w-[93%] md:w-[75%] mx-auto my-12"><div class="divider"></div></div>

    <x-sections.content-card
        title="Szempilla lifting"
        text="A szempilla lifting - vagy más néven szempilla dauer - egy olyan göndörítő kezelés, ami által a saját szempilláid íveltebbek lesznek, ez által hosszabbnak, a szemed pedig nagyobbnak fog tűnni. A lifting alatt a szempillákat be is festem tartós fekete festékkel. Kb. 4 – 6 hétig tartós."
        :features="[
            'allergiás vagy a műszempilla ragasztóra',
            'alternatívát keresel',
            'spirálozott hatást szeretnél elérni',
            'időt szeretnél spórolni',
            'hosszabb, feketébb szempillákat szeretnél'
        ]"
        priceDetails="<strong>Időtartam:</strong> 1 óra
        <strong>Ára:</strong> 12 000 Ft"
        image="/images/content/IMG_4767.jpg"
        reverse="true"
        width="w-[75%]"
    />

    <div class="w-[93%] md:w-[75%] mx-auto my-12"><div class="divider"></div></div>

    <x-sections.salon-info />

    <div class="w-[93%] md:w-[75%] mx-auto my-12"><div class="divider"></div></div>

    <x-sections.rich-text title="Konzultáció és javaslatok" width="w-[93%] md:w-[75%]">
        <p>Ahhoz, hogy a hozzád legjobban passzoló szettet készítsem el, szeretnélek megismerni egy kicsit. Ha új vendégként érkezel hozzám, akkor mindig tartunk egy rövid konzultációt. A pár perces konzultáció célja, hogy személyiségedhez, stílusodhoz és igényeidhez legjobban illő szempillákat készítsem el neked. Törekszem arra, hogy a legjobb szettet válasszuk ki számodra, amivel azonosulni tudsz és kényelmes viseletként szolgálhat a hétköznapokban is. Ha még sosem volt műszempillád, akkor arra is szánunk időt, hogy többet tudhass meg róla. Igyekszem olyan környezetet teremteni, ahol kényelmesen érezheted magad, kikapcsolhatsz és lazíthatsz miközben szépülsz. Új, hosszantartó szempilláid érdekében pedig azok megfelelő ápolását is megbeszéljük.</p>

        <p><strong>A szép és tartós műszempilla érdekében több tényezőt is figyelembe kell venni a szempillázás előtt és után is.</strong></p>

        <h3>Kezelés előtt:</h3>
        <ul>
            <li>Érdemes a pillázásra smink nélkül érkezni ugyanis az esetleges smink maradványok a szempillán gyengíthetik a ragasztó erősségét, ez által a tartósságot.</li>
            <li>A pillázást megelőző 1 napban nem ajánlott olajos krémeket, szérumokat használni az arcon ugyanis azok megtapadhatnak a szempillákon, ami kihathat a tartósságra.</li>
            <li>Tanácsos a hajmosást a pillázás előtti napra időzíteni, mivel 24 óráig nem érheti majd víz, gőz, pára stb a szempilláid.</li>
            <li>Más szakember munkája után nem vállalok töltést, hiszen mindenki más-más termékekkel dolgozik. Ebben az esetben a régi műszempilla leoldásra kerül és új szett kerül felhelyezésre.</li>
        </ul>

        <h3>Kezelés után:</h3>
        <ul>
            <li>Mint ahogy azt már fentebb említettem az építés után 24 óráig nem érheti víz, gőz, pára a szempilláid, ezen felül nem tanácsos ebben az időintervallumban szaunába vagy szoláriumba menni.</li>
            <li>Ajánlott minden nap reggel este fésülni a pilláid, a szempillafésűddel, melyet a felhelyezés után kapsz tőlem, ezen felül fontos, hogy rendszeresen mossuk a pillákat szempilla samponnal!</li>
            <li>Nem szabad dörzsölni a szemet, illetve tépkedni a műszempillákat.</li>
            <li>Sminkelésnél kerüljük az olajos sminklemosókat, mert oldhatják a ragasztót.</li>
        </ul>
    </x-sections.rich-text>

    <div class="w-[93%] md:w-[75%] mx-auto my-12"><div class="divider"></div></div>

    <x-sections.content-card
        title="Töltés"
        text="Ha valaki elsőre találkozik a műszempillával jogosan merülhet fel benne a kérdés, hogy mi az a töltés és miért van rá szükség. A természetes szempillák folyamatosan cserélődnek ez által a saját szempillával együtt kihullhatnak a műpillák, ami elsőre ijesztő lehet, de ez egy teljesen természetes folyamat. Minden szempilla más-más ütemben nő le. Egy ember szempilla állománya kb 90-120 nap alatt cserélődik ki. Egy hét alatt 3-5 szál saját szempilla hullik ki. Ez által ki lehet számolni, hogy a teljes állomány kb 50-70%-a 3 hét alatt kihullik. A lenőtt pillákat eltávolítjuk, a kihullott pillák helyére pedig újak nőnek ezekre pedig új műpillák kerülnek fel. Ezért van szükség töltésre.

        Amennyiben jelenleg nincs fent műszempillád akkor új szettre tudsz bejelentkezni majd a későbbiekben (2, 3 vagy 4 hétre) töltésre. Más által készített műszempillát nem áll módomban tölteni, ilyenkor az leoldásra kerül és új szett kerül felhelyezésre. Erre azért van szükség mert rengetegen dolgozunk ebben a szakmában és mindenki más-más termékkel dolgozik, ezért így tudok egységes és szép munkát kiadni a kezemből."
        image="/images/content/DSC_7854-min.jpg"
        width="w-[75%]"
    />
@endsection
