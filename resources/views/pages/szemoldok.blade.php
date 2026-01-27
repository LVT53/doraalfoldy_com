@extends('layouts.page')

@section('title', 'Szemöldök | Alföldy Dóra')

@section('page')
    <x-sections.service-hero 
        title="Szemöldök <span class='text-brand-gold'>Formázás</span>"
        category="Szemöldök"
        text="A szemöldök az egyik legfontosabb része az arcnak. Egy szép, jó ívű, színű és formájú szemöldök nagyban hozzájárul az arc kifejezőképességéhez."
        secondaryText="A szemöldök szálak növekedése, hossza, valamint az, hogy hova esik a szemöldökcsonton szintén befolyásoló tényező. Mindenkinek egyénre szabottan készítem el a számára előnyös ívű, formájú, színű szemöldököt úgy, hogy természetes hatású legyen."
        image="/images/content/B27A8D3E-DA84-4DE1-8309-EDFD27E61AC0-510x510.jpeg"
        imageAlt="Szemöldök munka"
        width="w-[75%]"
    />

    <div class="w-[93%] md:w-[75%] mx-auto my-12"><div class="divider"></div></div>

    <x-sections.content-card 
        title="Szemöldök"
        text="Ahhoz, hogy szép és tartós szemöldököt tudjak Neked készítsek több tényezőt is figyelembe kell venni a kezelés előtt és után is."
        featuresTitle="Kezelés előtt:"
        :features="[
            'Érdemes a hennás szemöldök festésre smink nélkül érkezni.',
            'A hennás szemöldök festést megelőző 1 napban nem ajánlott olajos krémeket, szérumokat használni az arcon ugyanis gyengítheti a festés tartósságát.',
            'Tanácsos a hajmosást a kezelés előtti napra időzíteni, mivel 24 óráig nem érheti majd víz, gőz stb a szemöldököd.'
        ]"
        priceDetails="<strong>Kezelés után:</strong>
        - Mint ahogy azt már említettem hennás szemöldök festés után 24 óráig nem érheti víz, gőz, pára a szemöldököd, ezen felül nem tanácsos ebben az időiintervallumban szoláriumba menni. 
        - Érdemes az olajos krémeket szemöldök festés után is kerülni 1 napig.
        - Nem szabad dörzsölni a szemöldöködet kezelés után mert gyengítheti a festés hatását."
        image="/images/content/4C57FC68-7D51-44F6-9CD6-51F1C9D78240-2-scaled-2048x2048-min.jpg"
        width="w-[75%]"
    />

    <div class="w-[93%] md:w-[75%] mx-auto my-12"><div class="divider"></div></div>

    <section class="w-[93%] md:w-[75%] mx-auto grid grid-cols-1 md:grid-cols-2 gap-12 lg:gap-16 mb-24">
        <x-sections.pricing-card 
            title="Cérnás szemöldök formázás és hennás szemöldök festés"
            text="A hennás szemöldök festés olyanoknak ajánlott, akik tartós és esztétikus szemöldökre vágynak. Ez a kezelés egy cérnás szemöldök szedésből, formázásból és hennás szemöldök festésből áll. A henna festék bőrön bőrtípustól függően 1-2 hétig tartós, a szemöldök szálakon pedig akár 3-4 hétig is. Több mint 70%-ban természetes hatóanyagokat tartalmaz."
            image="/images/content/image1-510x765.jpeg"
            :features="['tartós megoldást keresel szemöldököd festésére', 'nem szeretnél kitetováltatni a szemöldöködet', 'rendezett és esztétikus szemöldökre vágysz', 'netán túlszedték a szemöldököd és időt szeretnél spórolni']"
            priceDetails="<strong>Időtartam:</strong> 30 – 50 perc
            <strong>Ára:</strong> 
            Cérnás és csipeszes szemöldök szedés és hennás festés: 6 500 Ft
            Cérnás és csipeszes szemöldök szedés: 3 500 Ft
            Hennás szemöldök festés: 4 000 Ft"
        />

        <x-sections.pricing-card 
            title="Szemöldök laminálás"
            text="Egy ideje szemezgettem a szemöldök laminálással, illetve többen is érdeklődtetek már erről ezért nem rég elvégeztem egy képzést. Így márciustól már elérhető a szemöldök laminálás, ami a legkuszább szemöldök szálakat is a helyes irányba tudja rendezni."
            image="/images/content/IMG_2250-scaled-2048x2048-min.jpg"
            :features="['vastagabb dúsabb szemöldökre vágysz', 'rendezett szemöldökre vágysz', 'nem vagy elégedett a szemöldököd formájával']"
            priceDetails="<strong>Időtartam:</strong> 45 perc – 1 óra
            <strong>Ára:</strong>
            Szemöldök laminálás: 7 500 Ft
            Szemöldök laminálás és szemöldök szedés: 8 500 Ft
            Szemöldök laminálás szemöldök szedéssel és hennás festéssel: 9 500 Ft"
        />
    </section>

    <div class="w-[75%] mx-auto my-12"><div class="divider"></div></div>

    <x-sections.content-card 
        title="Szemöldök festés"
        text="Az előkészítés után következhet a hennás festés. Teljes mértékben személyre szabottan, az igényeidnek megfelelően keverem ki a szint. Több színből tudsz választani, amelyeket keverni is tudunk a tökéletes eredményérdekében. A festés mindössze 10-15 percet vesz igénybe a száradási idővel együtt."
        image="/images/content/image0-510x287.jpeg"
        reverse="true"
        width="w-[75%]"
    />

    <div class="w-[75%] mx-auto my-12"><div class="divider"></div></div>

    <x-sections.content-card 
        title="Utókezelés"
        text="A szemöldök festés után fontos, hogy amennyiben lehetséges ne érje víz 24 óráig a szemöldököt. A tartósság érdekében az egyik legfontosabb, hogy ne dörzsöld a szemöldököd mert annak hatására a festés megkophat. Sminklemosó nyugodtan érheti, de ügyelni kell ott is a finom mozdulatokra."
        image="/images/content/DSC_4461-min.jpg"
        width="w-[75%]"
    />

    <div class="w-[93%] md:w-[75%] mx-auto my-12"><div class="divider"></div></div>

    <x-sections.salon-info />
@endsection
