@extends('layouts.page')

@section('title', 'Smink tanácsadás | Alföldy Dóra')

@section('page')
    <x-sections.service-hero
        title="Smink <span class='text-brand-gold'>Tanácsadás</span>"
        category="Workshop"
        text="A személyre szabott smink magabiztossá és kiegyensúlyozottá tesz minket nőket, ami által jobban érezzük magukat a bőrünkben."
        secondaryText="Egy sminkben rengetek lehetőség rejlik és ha belejövünk és megismerjük az arcunk, a vonásaink akkor kedvünk szerint alakíthatjuk. Lehetünk elegáns, kifinomult hölgyek vagy csinos, szexi nők."
        image="/images/content/IMG_0027-min.jpg"
        imageAlt="Smink tanácsadás"
        width="w-[75%]"
    />

    <div class="w-[93%] md:w-[75%] mx-auto my-12"><div class="divider"></div></div>

    <x-sections.content-card
        title="Smink tanácsadás"
        text="A mindennapi rohanás és a sok teendő mellett néha lassíts, és lélegezz fel egy kicsit. Kapcsolj ki, lazíts és törődj magaddal. Ebben szeretnék Neked segíteni. Egy olyan smink tanácsadást álmodtam meg, ahol egyszerre tudsz kikapcsolni és tanulni. Az alapoktól kezdve elsajátíthatod a számodra legtökéletesebb nappali smink csínját-bínját. Lesd el tőlem a profi trükköket, hozd ki magadból a legjobbat!"
        priceDetails="<strong>Időtartam:</strong> 2 – 3 óra
        <strong>Ára:</strong> 30 000 Ft"
        image="/images/content/IMG_0005-510x765.jpeg"
        buttonHref="https://doraalfoldy.salonic.hu/"
        width="w-[75%]"
    />

    <div class="w-[93%] md:w-[75%] mx-auto my-12"><div class="divider"></div></div>

    <x-sections.content-card
        title="Ismerd meg önmagad!"
        text="Ahhoz, hogy tökéletes natúr sminket sajátíthasd el először meg kell ismerned önmagad. Az adottságaid, a vonásaid és a belső éned. Ezen az úton segítségedre leszek, hogy közösön megtaláljuk a számodra legszebb és legpraktikusabb sminket, melyet könnyen elkészíthetsz.

        Ha szeretnél ezt az időt megosztani valakivel akkor hozd magaddal a barátnőd, testvéred vagy édesanyád és vegyétek igénybe kedvezményesen a sminktanácsadást."
        featuresTitle="Miért válassz engem?"
        :features="[
            'Új dolgokat tanulhatsz és sajátíthatsz el jó hangulatban',
            'Igényes, rendezett és ízléses szalonban tanulhatsz',
            'Rugalmas időpontegyeztetés – akár hétvégén is!',
            'Megismerkedhetsz új, professzionális smink termékekkel és eszközökkel'
        ]"
        image="/images/content/IMG_0009-510x765.jpg"
        reverse="true"
        width="w-[75%]"
    />

    <div class="w-[93%] md:w-[75%] mx-auto my-12"><div class="divider"></div></div>

    <x-sections.content-card
        title="Mit tudsz elsajátítani a sminktanácsadás során?"
        text="<strong>Személyre szabott sminket, ami a legjobban illik hozzád</strong>
        A tanácsadás során elkészítjük a számodra legtökéletesebb, legkényelmesebb sminket, amely mindenhol megállja a helyét. Legyen az egy vacsora vagy egy nagyobb esemény.

        <strong>Egy egyszerű smink technikát</strong>
        Megmutatom azokat a technikákat, amellyel bárhol, bármilyen körülmények között ki tudod majd magad sminkelni.

        <strong>A termékek, eszközök listája, amelyre szükséged lesz a sminked elkészítéséhez</strong>
        A sminktanácsadás során felhasznált eszközöket és termékeket listába szedjük és összeírjuk, hogy a vásárláskor könnyebb dolgod legyen.

        <strong>Átfogó, jól érthető tananyag</strong>
        A közösen eltöltött idő alatt minden kérdésedre választ kapsz annak érdekében, hogy a tanultak megragadjanak és azt a jövőben használni tudd a mindennapokban."
        image="/images/content/IMG_0020-510x765.jpg"
        width="w-[75%]"
    />

    <div class="w-[93%] md:w-[75%] mx-auto my-12"><div class="divider"></div></div>

    <x-sections.salon-info />
@endsection
