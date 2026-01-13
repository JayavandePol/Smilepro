Hier is het aangepaste requirements-document voor de **Create**-feature (Sprint 02).

Ik heb de inhoud getransformeerd van "gegevens ophalen" (Read) naar "gegevens invoeren en opslaan" (Create), inclusief formuliervalidatie en `INSERT` stored procedures, maar met behoud van dezelfde structuur en beoordelingscriteria als je aangeleverde rubric.

---

# Requirements – Create Feature (Sprint 02 – Periode 02)

Dit document beschrijft alle functionele en technische vereisten voor het **Create**-gedeelte van de applicatie. De richtlijnen zijn geschreven zodat een AI of developer gestructureerd, compleet en correct code kan genereren conform de wensen van de opleiding.

---

## 1. Functionele Requirements

### ✔ 1.1 Happy Scenario – Create

De applicatie moet succesvol nieuwe data kunnen ontvangen van de gebruiker en opslaan in de database.

**Voorbeeld:**

* Gebruiker bezoekt `/products/create` → formulier wordt getoond.
* Gebruiker vult formulier in en klikt op "Opslaan".
* Data wordt verwerkt via een Controller (`ProductController@store`).
* Data wordt opgeslagen in de database.

```php
// Controller
public function store(Request $request)
{
    // Validatie en opslaan logica
    Product::create($request->all());
    return redirect()->route('products.index');
}

```

---

### ✔ 1.2 End-user Feedback (Happy Scenario)

Na een succesvolle create-actie ontvangt de gebruiker een visuele melding.

**Voorbeeld:**

```blade
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

```

Controller voegt melding toe na opslaan:

```php
return redirect()->route('products.index')->with('success', 'Product succesvol aangemaakt.');

```

---

### ✔ 1.3 Unhappy Scenario – Create

Als het opslaan mislukt (bijv. validatiefout of databasefout), moet de gebruiker geïnformeerd worden en de invoer behouden blijven (indien mogelijk).

**Voorbeeld:**

```php
$validated = $request->validate([
    'name' => 'required|max:255',
    'price' => 'required|numeric',
]);
// Bij falen wordt automatisch terugverwezen met $errors

```

**In Blade (Validatie meldingen):**

```blade
<input type="text" name="name" class="@error('name') border-red-500 @enderror">
@error('name')
    <div class="text-red-500">{{ $message }}</div>
@enderror

```

---

### ✔ 1.4 End-user Feedback (Unhappy Scenario)

Foutmeldingen (zoals "Veld is verplicht") moeten duidelijk leesbaar zijn bij de betreffende invoervelden.

---

## 2. UI/UX Requirements

### ✔ 2.1 Responsive Design (Formulier)

Het invulformulier moet volledig responsive zijn met behulp van **TailwindCSS**.

**Voorbeeld:**

```blade
<form action="{{ route('products.store') }}" method="POST" class="max-w-lg mx-auto p-4 bg-white shadow rounded">
    @csrf
    <div class="mb-4">
        <label class="block text-gray-700 font-bold mb-2">Product Naam</label>
        <input type="text" name="name" class="w-full px-3 py-2 border rounded">
    </div>
    <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600">
        Opslaan
    </button>
</form>

```

---

## 3. Architectuur Requirements

### ✔ 3.1 Uitleg MVC-architectuur verplicht

AI moet de structuur van het patroon volgen voor Create:

* **View (Blade)** → `create.blade.php` bevat het HTML-formulier.
* **Controller** → `store()` method handelt de `POST` request af en roept het Model aan.
* **Model** → `Product` model regelt de `INSERT` in de database (mass assignment).

**Voorbeeldmap:**

```
resources/views/products/create.blade.php
app/Http/Controllers/ProductController.php (method: create & store)
app/Models/Product.php ($fillable)

```

---

### ✔ 3.2 UML Klassediagram (Create)

Er moet een UML-schema aanwezig zijn. De flow van invoer naar database moet duidelijk zijn.

Voorbeeld beschrijving:

* `ProductController` heeft method `store(Request $request)`
* `Product` Model heeft properties die overeenkomen met de formuliervelden.

---

## 4. Codekwaliteit Requirements

### ✔ 4.1 Commentaar in de Code

Elke functie in Controller (create/store) moet kort en duidelijk gedocumenteerd zijn.

```php
/**
 * Toont het formulier om een nieuw product aan te maken.
 */
public function create() { ... }

/**
 * Valideert de invoer en slaat het nieuwe product op in de database.
 */
public function store(Request $request) { ... }

```

---

### ✔ 4.2 Gebruik van Relaties/Joins (in Create Context)

Bij het aanmaken van een item moet vaak een relatie gekozen worden (bijv. Categorie kiezen via een dropdown). Hiervoor is een **join** of relationele query nodig om de opties op te halen.

**Eloquent voorbeeld:**

```php
// In de create method
$categories = Category::all(); // Haalt data op voor de <select> opties
return view('products.create', compact('categories'));

```

**Blade:**

```blade
<select name="category_id">
    @foreach($categories as $category)
        <option value="{{ $category->id }}">{{ $category->name }}</option>
    @endforeach
</select>

```

---

### ✔ 4.3 Gebruik van Stored Procedures

De applicatie moet gegevens kunnen opslaan via een Stored Procedure (INSERT).

**Voorbeeld (MySQL):**

```sql
CREATE PROCEDURE InsertProduct(IN p_name VARCHAR(255), IN p_price DECIMAL(10,2))
BEGIN
    INSERT INTO products (name, price) VALUES (p_name, p_price);
END;

```

**Laravel:**

```php
DB::statement('CALL InsertProduct(?, ?)', [$request->name, $request->price]);

```

---

### ✔ 4.4 Try/Catch verplicht

Foutafhandeling moet toegepast worden bij het opslaan.

```php
try {
    Product::create($validatedData);
} catch (QueryException $e) {
    Log::error("Fout bij aanmaken product: " . $e->getMessage());
    return redirect()->back()->with('error', 'Database fout opgetreden.');
}

```

---

### ✔ 4.5 PSR-12 Code Conventions

AI moet code uitvoeren volgens:

* Validatie logica netjes gegroepeerd.
* Duidelijke inspringing in arrays en HTML-attributen.
* Correct gebruik van `Request` object injection.

---

### ✔ 4.6 Passende Naamgeving

Functies en variabelen moeten betekenisvol zijn.

**Goed:** `$newProduct`, `storeProduct()`, `$validatedAttributes`

**Slecht:** `$p`, `saveIt()`, `$data`

---

### ✔ 4.7 Technische Log

Acties (succesvol aangemaakt) en fouten moeten gelogd worden.

```php
Log::info('Nieuw product aangemaakt: ' . $product->id);
Log::error('Create actie mislukt: ' . $e->getMessage());

```

---

### ✔ 4.8 Minimaal 10 Commits

Repository moet minimaal **10 commits** bevatten met duidelijke en beschrijvende messages over de create-functionaliteit.

**Goed commitvoorbeeld:**

```
feat: implement create form layout with tailwind
feat: add store method with validation logic
fix: resolve issue with stored procedure parameters

```

---

## 5. Eindscore

Totale punten: **27 / 27**

---

## Doel van dit Document

Dit `requirements.md` bestand is geüpdatet voor **Sprint 02 (Create)** en kan direct worden gebruikt door AI-modellen of developers om de code te genereren die voldoet aan de beoordelingsrubriek voor het aanmaken van gegevens.