# Requirements – Read Feature (Sprint 01 – Periode 02)

Dit document beschrijft alle functionele en technische vereisten voor het **Read**-gedeelte van de applicatie, gebaseerd op de sprintreview-criteria. De richtlijnen zijn geschreven zodat een AI of developer gestructureerd, compleet en correct code kan genereren conform de wensen van de opleiding.

---

## 1. Functionele Requirements

### ✔ 1.1 Happy Scenario – Read

De applicatie moet succesvol data kunnen ophalen en tonen aan de eindgebruiker.

**Voorbeeld:**

* Gebruiker bezoekt `/products` → lijst van producten wordt getoond.
* Data wordt opgehaald via een Controller (`ProductController@index`).
* Blade-template toont de resultaten in een tabel of kaartweergave.

```php
// Controller
public function index()
{
    $products = Product::all();
    return view('products.index', compact('products'));
}
```

---

### ✔ 1.2 End-user Feedback (Happy Scenario)

Na een succesvolle read-actie ontvangt de gebruiker een visuele melding.

**Voorbeeld:**

```blade
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
```

Controller voegt melding toe:

```php
return redirect()->back()->with('success', 'Producten succesvol geladen.');
```

---

### ✔ 1.3 Unhappy Scenario – Read

Als het ophalen van data mislukt (bijv. databasefout of geen resultaten), moet een foutmelding worden getoond.

**Voorbeeld:**

```php
try {
    $products = Product::all();
    if ($products->isEmpty()) {
        throw new Exception("Geen producten gevonden.");
    }
} catch (Exception $e) {
    return redirect()->back()->with('error', $e->getMessage());
}
```

**In Blade:**

```blade
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
```

---

### ✔ 1.4 End-user Feedback (Unhappy Scenario)

Foutmeldingen moeten duidelijk leesbaar zijn en de gebruiker informeren over het probleem.

---

## 2. UI/UX Requirements

### ✔ 2.1 Responsive Design

De webapplicatie moet volledig responsive zijn met behulp van **TailwindCSS**.

**Voorbeeld:**

```blade
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
    @foreach($products as $product)
        <div class="p-4 bg-white rounded shadow">
            <h2 class="text-lg font-bold">{{ $product->name }}</h2>
        </div>
    @endforeach
</div>
```

---

## 3. Architectuur Requirements

### ✔ 3.1 Uitleg MVC-architectuur verplicht

AI moet de structuur van het patroon volgen:

* **Model** → database interacties
* **View (Blade)** → presentatie
* **Controller** → logica tussen View en Model

**Voorbeeldmap:**

```
app/Models/Product.php
app/Http/Controllers/ProductController.php
resources/views/products/index.blade.php
```

---

### ✔ 3.2 UML Klassediagram (Read)

Er moet een UML-schema aanwezig zijn. AI moet de structuur kunnen afleiden.

Voorbeeld beschrijving:

* `Product` heeft velden: id, name, price, stock
* `ProductController` gebruikt `Product` model om data op te halen

---

## 4. Codekwaliteit Requirements

### ✔ 4.1 Commentaar in de Code

Elke functie in Controller en Model moet kort en duidelijk gedocumenteerd zijn.

```php
/**
 * Haalt alle producten op uit de database.
 */
public function index() { ... }
```

---

### ✔ 4.2 Gebruik van Joins

Read-functionaliteit moet minimaal één JOIN-query bevatten.

**Eloquent voorbeeld:**

```php
$products = Product::select('products.*', 'categories.name AS category')
    ->join('categories', 'categories.id', '=', 'products.category_id')
    ->get();
```

---

### ✔ 4.3 Gebruik van Stored Procedures

De applicatie moet gegevens kunnen ophalen via een Stored Procedure.

**Voorbeeld (MySQL):**

```sql
CREATE PROCEDURE GetAllProducts()
BEGIN
    SELECT * FROM products;
END;
```

**Laravel:**

```php
$products = DB::select('CALL GetAllProducts()');
```

---

### ✔ 4.4 Try/Catch verplicht

Foutafhandeling moet altijd worden toegepast.

```php
try {
    $data = DB::table('products')->get();
} catch (QueryException $e) {
    Log::error($e->getMessage());
}
```

---

### ✔ 4.5 PSR-12 Code Conventions

AI moet code uitvoeren volgens:

* duidelijke inspringing
* juiste spacing
* snake_case voor databasevelden
* camelCase voor functions & variables

---

### ✔ 4.6 Passende Naamgeving

Functies en variabelen moeten betekenisvol zijn.

**Goed:** `$availableProducts`, `loadProductData()`

**Slecht:** `$x`, `get1()`

---

### ✔ 4.7 Technische Log

Acties en fouten moeten gelogd worden.

```php
Log::info('Producten succesvol opgehaald.');
Log::error('Database fout bij ophalen producten: ' . $e->getMessage());
```

---

### ✔ 4.8 Minimaal 10 Commits

Repository moet minimaal **10 commits** bevatten met duidelijke en beschrijvende messages.

**Goed commitvoorbeeld:**

```
feat: implement read functionality with joins and stored procedures
fix: add error handling for empty dataset
refactor: improve controller naming conventions
```

---

## 5. Eindscore

Totale punten: **27 / 27**

---

## Doel van dit Document

Dit `requirements.md` bestand kan direct worden gebruikt door AI-modellen of developers om correcte code te genereren die voldoet aan de beoordelingsrubriek.
