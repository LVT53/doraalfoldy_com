@extends('layouts.page')

@section('title', 'Smink | Alföldy Dóra')

@section('page')
    <x-sections.service-hero 
        title="Smink <span class='text-brand-gold'>Design</span>"
        category="Smink"
        text="Hiszek abban, hogy egy személyre szabott smink kiemel és nem eltakar. A vonásaid, a stílusod és a karaktered kerül kiemelésre."
        secondaryText="Törekszem arra, hogy a sminkelés során megismerjelek téged annyira, hogy a legtökéletesebb sminket tudjam elkészíteni neked."
        image="/images/content/DSC_4145-min.jpg"
        imageAlt="Smink munka"
        width="w-[75%]"
    />

    <div class="w-[93%] md:w-[75%] mx-auto"><div class="divider"></div></div>

    <x-sections.content-card 
        title="Alkalmi smink"
        text="Életünket számtalan kisebb-nagyobb esemény kíséri végig. Lehet az egy baráti összejövetel, születésnap, esküvő, szalagavató vagy akár egy fotózás. Olyan fontos pillanatok, melyeken szeretünk szépek, csinosak és elegánsak lenni. Ha már van elképzelésed, akkor profi segítséget nyújtok hozzá. Ha pedig teljesen rám bízod magad, akkor is biztos lehetsz abban, hogy elégedetten és mosollyal az arcodon távozol tőlem."
        priceDetails="<strong>Időtartam:</strong> 1 óra
        <strong>Ára:</strong> 15 000 Ft"
        image="/images/content/C9AF1BEC-B436-40CF-B5F1-45CA0D255AD3-min.jpg"
        buttonHref="https://doraalfoldy.salonic.hu/"
        width="w-[75%]"
    />

    <div class="w-[93%] md:w-[75%] mx-auto"><div class="divider"></div></div>

    <x-sections.content-card 
        title="Esküvői smink"
        text="Ez a nap az egyik legfontosabb esemény egy nő életében, melyet dédelgetve szervez, tűkön ülve várja és amelyen a legszebb, legjobb formáját szeretné nyújtani. Ezen a napon a dekortól kezdve, a zenén és a sminken át mindennek meg van a maga szerepe. Az én feladatom ezen a napon a jókedv biztosítása mellett az ara sminke, amelyet mindig a menyasszony személyiségére szabok. Egy nagy napi smink eleganciát, ártatlanságot és szépséget tükröz. Mindezek mellett fontos, hogy a vőlegény tetszését is elnyerje. A tökéletes végeredmény érdekében lehetőség van próba smink elkészítésére, ahol mindent megbeszélünk a menyaszonnyal, hogy az elképzeléseit maximálisan létre tudjuk hozni. A próba smink a budapesti szalonban készül el, a nagynapi pedig a készülődés helyszínén."
        priceDetails="<strong>Próba smink</strong>
        Helyszín: Szalon – Budapest
        Időtartam: 1.5 – 2 óra
        Ára: 20 000 Ft

        <strong>Nagynapi smink</strong>
        Helyszín: Esküvő – készülődési hely
        Időtartam: 1.5 óra
        Ára: 30 000 Ft

        Az esküvő napján a hozzátartozók, koszorúslányok sminkjét is szívesen elkészítem. Ennek ára 15 000 Ft/koszorúslány, hozzátartozó; 12 000 Ft/örömanya.

        Érdeklődni az <a href='mailto:info@doraalfoldy.com' class='text-brand-gold font-bold hover:underline'>info@doraalfoldy.com</a> címen tudsz dátum és helyszín megjelöléssel, valamint a sminkelendő személyek számával. Ezzel is gyorsítva az árajánlat adást."
        image="/images/content/DSC_4522-790x1183-min.jpg"
        reverse="true"
        width="w-[75%]"
    />

    <div class="w-[93%] md:w-[75%] mx-auto"><div class="divider"></div></div>

    <x-sections.content-card 
        title="Smink fotózásra"
        text="Egy fotózásnak külön hangulata van. A pörgés, a zene, a csapat, az alkotás. Az ilyen események precizitást és alkalmazkodást igényelnek. Szívesen részt veszek különböző kreatív anyagokban, legyen szó egy natúrabb vagy egy extrább fotózásról. Emellett az ötletelésben és a szervezésben is szívesen segédkezem."
        priceDetails="<strong>Időtartam:</strong> 1 – 1.5 óra
        <strong>Ára:</strong> 18 000 Ft (készenléti díj 5000 Ft/megkezdett óra)

        A kiszállási díj a helyszíntől függ ezért kérlek érdeklődj emailben."
        image="/images/content/DSC_44381-510x764.jpg"
        buttonHref="https://doraalfoldy.salonic.hu/"
        width="w-[75%]"
    />

    <div class="w-[93%] md:w-[75%] mx-auto"><div class="divider"></div></div>

    <x-sections.content-card 
        title="Szalagavató smink"
        text="Egy szalagavató egyszeri és megismételhetetlen alkalom egy fiatal nő életében. A felnőtté válásunk egyik legmeghatározóbb pillanata. Tedd varázslatossá az estét egy profi sminkkel!"
        priceDetails="<strong>Időtartam:</strong> 1 – 1.5 óra
        <strong>Ára:</strong> 18 000 Ft

        A kiszállási díj helyszíntől függ ezért kérlek érdeklődj emailben."
        image="/images/content/IMG_133-2-scaled-min.jpg"
        reverse="true"
        buttonHref="https://doraalfoldy.salonic.hu/"
        width="w-[75%]"
    />

    <div class="w-[93%] md:w-[75%] mx-auto"><div class="divider"></div></div>

    <x-sections.salon-info />
@endsection
