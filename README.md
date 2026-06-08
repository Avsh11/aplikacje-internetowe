# Capitex

## Temat projektu

Tematem projektu jest aplikacja webowa Capitex, która umożliwia śledzenie własnych inwestycji, gdzie transakcja ich została dokonana na wielu platformach/portfelach np. XTB, Binance itd. Użytkownik rejestruje konto, tworzy portfele (broker, giełda, alternatywy), dodaje transakcje kupna i obserwuje wartość portfela, niezrealizowany zysk/stratę oraz wykresy. Administrator zarządza kontami użytkowników w systemie. Aplikacja rozwiązuje problem braku jednego miejsca monitorowania naszych inwestycji. Istnieją już podobne rozwiązania takie jak chociażby aplikacja getquin natomiast nasza aplikacja w swojej pierwszej wersji wyróżnia się minimalizmem i łatwym zarządzaniem użytkownikami po stronie administratora, gdzie ten ma swój własny panel wraz z możliwością monitorowania własnych inwestycji.

## Uruchomienie projektu (developer)

Kod aplikacji znajduje się w katalogu `capitex/` (w repozytorium `aplikacje-internetowe`).

## Użyte technologie

| Komponent | Technologia | Wersja | Zastosowanie | Link |
| :--- | :--- | :--- | :--- | :--- |
| Backend | PHP | 8.3+ (testowane: **8.5.3**) | Logika serwera, API wewnętrzne | [php.net](https://www.php.net/) |
| Framework | Laravel | **13.11.1** (^13.8) | MVC, routing, ORM, migracje | [laravel.com](https://laravel.com/) |
| Autentykacja | Laravel Breeze | ^2.4 | Logowanie, rejestracja, sesje | [laravel.com/docs/starter-kits](https://laravel.com/docs/starter-kits) |
| Baza danych | SQLite | 3.x | Plik `database/database.sqlite` | [sqlite.org](https://www.sqlite.org/) |
| Menedżer PHP | Composer | 2.x (np. **2.9.x**) | Zależności PHP (`composer install`) | [getcomposer.org](https://getcomposer.org/) |
| Runtime JS | Node.js | 22+ (zalecane) | Build frontendu (Vite) | [nodejs.org](https://nodejs.org/) |
| Menedżer JS | npm | 10+ | Pakiety JavaScript (`npm install`) | [npmjs.com](https://www.npmjs.com/) |
| Bundler | Vite | ^8.0 | Kompilacja CSS/JS (`npm run build`) | [vitejs.dev](https://vitejs.dev/) |
| CSS / UI | Bootstrap | ^5.3.8 | Layout, komponenty, modale | [getbootstrap.com](https://getbootstrap.com/) |
| Ikony | Bootstrap Icons | 1.11.3 | Ikony w panelu użytkownika i admina | [icons.getbootstrap.com](https://icons.getbootstrap.com/) |
| Wykresy | Chart.js | CDN (jsdelivr) | Wykres liniowy i donut na dashboardzie | [chartjs.org](https://www.chartjs.org/) |
| Zapytania AJAX | Fetch API (natywny JS) | — | Autocomplete aktywów, ceny, kursy NBP w dashboardzie | [MDN: fetch](https://developer.mozilla.org/pl/docs/Web/API/Fetch_API) |

**API zewnętrzne (bez konfiguracji w `.env`):**
- Binance – ceny kryptowalut
- Yahoo Finance – ceny akcji/ETF, wyszukiwanie tickerów
- NBP – kursy walut (tabela A)

### Wymagania programowe

#### System operacyjny
- Projekt rozwijany i testowany na **Microsoft Windows 11 Pro** (działa też na Linux/macOS).

#### Środowisko uruchomieniowe
- **PHP 8.3+** z rozszerzeniami: `pdo_sqlite`, `mbstring`, `openssl`, `fileinfo` (avatary), zalecane `curl` (zapytania do API).
- **Composer 2.x**
- **Node.js 22+** i **npm**
- **Git** (klonowanie repozytorium)

#### Baza danych
- **SQLite** – nie wymaga osobnego serwera (MySQL/PostgreSQL opcjonalnie po zmianie `DB_*` w `.env`).

#### Dodatkowe narzędzia 
- Przeglądarka (Chrome, Firefox, Edge)
- Połączenie z internetem (pobieranie cen z API)
- Edytor kodu (np. VS Code / Cursor) – opcjonalnie w dokumentacji



### Proces instalacji

Poniższa instrukcja zakłada, że osoba uruchamiająca projekt **nie zna Laravela**. Wszystkie komendy wykonuj w katalogu `capitex/` (cd capitex/).

#### Krok 1: Pobranie projektu

1. Otwórz terminal (PowerShell, CMD lub terminal w VS Code).
2. Przejdź do folderu, w którym ma znaleźć się projekt.
3. Sklonuj repozytorium:

```bash
git clone <URL_TWOJEGO_REPOZYTORIUM>
cd aplikacje-internetowe/capitex
```

Lub poprzez aplikację **GitHub Desktop**, gdzie proces jest zautomatyzowany

> Zamień `<URL_TWOJEGO_REPOZYTORIUM>` na link do repozytorium Git (GitHub / GitLab).

#### Krok 2: Instalacja zależności PHP

Laravel i biblioteki PHP instalujesz przez Composer:

```bash
composer install
```

Komenda pobiera pakiety z pliku `composer.json` do folderu `vendor/`.

#### Krok 3: Instalacja zależności frontendu

Style i skrypty (Bootstrap, Vite) buduj przez npm:

```bash
npm install
```

Komenda pobiera pakiety z pliku `package.json` do folderu `node_modules/`.

**Alternatywa – jednorazowy setup** (wykonuje kroki 2–3 oraz część konfiguracji):

```bash
composer run setup
```



### Proces konfiguracji

Przed pierwszym uruchomieniem aplikacji wykonaj poniższe kroki (nadal w katalogu `capitex/`).

#### 1. Zmienne środowiskowe

Skopiuj przykładowy plik konfiguracyjny i wygeneruj klucz aplikacji:

```bash
# Windows (CMD / PowerShell)
copy .env.example .env

php artisan key:generate
```

Na Linux/macOS zamiast `copy` użyj: `cp .env.example .env`

Najważniejsze ustawienia w pliku `.env`:

| Zmienna | Opis | Przykładowa wartość |
|---------|------|---------------------|
| `APP_URL` | Adres, pod którym otwierasz aplikację w przeglądarce | `http://127.0.0.1:8000` |
| `APP_DEBUG` | Tryb deweloperski (szczegółowe błędy) | `true` |
| `DB_CONNECTION` | Typ bazy danych | `sqlite` |

> **Uwaga:** Capitex **nie wymaga kluczy API** w `.env`. Integracje z Binance, Yahoo Finance i NBP korzystają z publicznych endpointów – nie trzeba nic wpisywać do pliku `.env` poza standardową konfiguracją Laravela.

#### 2. Baza danych

Projekt domyślnie używa **SQLite** (jeden plik zamiast serwera MySQL).

1. Upewnij się, że w `.env` jest:

```env
DB_CONNECTION=sqlite
```

2. Utwórz pusty plik bazy (jeśli nie istnieje):

```bash
# Windows
type nul > database\database.sqlite

# Linux / macOS
touch database/database.sqlite
```

To jest całe „połączenie z bazą” – Laravel zapisuje dane w pliku `database/database.sqlite`.

#### 3. Migracje

Migracje tworzą strukturę tabel w bazie (użytkownicy, portfele, transakcje itd.):

```bash
php artisan migrate
```

#### 4. Dane początkowe (seed)

Główny seeder (`DatabaseSeeder`) wypełnia bazę danymi startowymi:

| Co tworzy | Plik | Opis |
|-----------|------|------|
| Role `admin` / `user` | `DatabaseSeeder` | Słownik ról w systemie |
| Konto administratora | `DatabaseSeeder` | Jedno konto z pełnymi uprawnieniami |
| **100 kont testowych** | `UserSeeder` | Losowe polskie imiona i unikalne maile (Faker `pl_PL`), **bez portfeli i transakcji** |
| Słownik aktywów | `AssetSeeder` | Globalne tickery (BTC, AAPL, CDR) – nie przypisane do użytkowników |

**Pełny reset bazy + seed** (zalecane przy pierwszym uruchomieniu i przed zdjęciami do dokumentacji):

```bash
php artisan migrate:fresh --seed
```

Tylko seed na istniejącej strukturze tabel (bez kasowania danych):

```bash
php artisan db:seed
```

> **Uwaga:** `UserSeeder` generuje **nowe** maile przy każdym uruchomieniu. Ponowne `db:seed` bez `migrate:fresh` może skończyć się błędem duplikatów – wtedy użyj `migrate:fresh --seed` albo uruchom tylko brakujące seedery na czystej bazie.

Tylko 100 użytkowników testowych (gdy role i admin już istnieją w bazie):

```bash
php artisan db:seed --class=UserSeeder
```

Dodatkowo utwórz symlink do folderu z plikami publicznymi (wymagany do **avatarów** użytkowników):

```bash
php artisan storage:link
```

**Konto administratora po seedzie:**

| Pole | Wartość |
|------|---------|
| Email | `admin@capitex.pl` |
| Hasło | `admin` |

**Konta testowe z `UserSeeder` (100 szt.):**

| Pole | Wartość |
|------|---------|
| Rola | `user` (zwykły użytkownik) |
| Nazwa / email | Losowe, generowane przez Faker (np. `jan.kowalski@example.com`) |
| Hasło | `password` (wspólne dla wszystkich kont testowych) |
| Portfele / transakcje | Brak – puste konta pod listę w panelu admina i statystyki |

Zwykły użytkownik może też założyć konto samodzielnie przez stronę rejestracji (`/register`) – otrzymuje rolę `user`.

#### 5. Build frontendu

Jeśli nie używałeś `composer run setup`, zbuduj style i skrypty przed uruchomieniem:

```bash
npm run build
```



### Uruchomienie projektu w terminalu

**Wariant podstawowy (wystarczający do testów i obrony):**

```bash
php artisan serve
```

Aplikacja będzie dostępna w przeglądarce pod adresem:

**http://127.0.0.1:8000**

Przydatne adresy po uruchomieniu:

| Strona | Adres |
|--------|-------|
| Strona startowa | http://127.0.0.1:8000/ |
| Logowanie | http://127.0.0.1:8000/login |
| Rejestracja | http://127.0.0.1:8000/register |
| Dashboard (po zalogowaniu) | http://127.0.0.1:8000/dashboard |
| Panel administratora | http://127.0.0.1:8000/admin/dashboard |

**Wariant deweloperski** (serwer PHP + podgląd na żywo zmian CSS/JS):

```bash
composer run dev
```

(lub w dwóch terminalach osobno: `php artisan serve` oraz `npm run dev`)

> Ustaw `APP_URL` w `.env` zgodnie z adresem, pod którym faktycznie otwierasz aplikację (np. `http://127.0.0.1:8000`), szczególnie jeśli używasz avatarów.

## Uruchomienie projektu (user)

Capitex to **aplikacja webowa** – nie wymaga instalacji pliku `.exe` ani `.apk`. Wystarczy nowoczesna przeglądarka i dostęp do internetu.

### Dostęp do aplikacji

W pierwszej wersji projektu aplikacja **nie jest jeszcze opublikowana** w publicznej sieci (brak stałego adresu produkcyjnego). Do testów i demonstracji używa się wersji uruchomionej lokalnie przez programistę (`php artisan serve` → `http://127.0.0.1:8000`).

> Po wdrożeniu na serwer (np. hosting PHP, VPS, Laravel Forge) w tym miejscu należy podać **publiczny link**, np. `https://capitex.example.com`.

### Jak korzystać z aplikacji (bez instalacji)

1. Otwórz w przeglądarce adres aplikacji (lokalny lub publiczny).
2. Załóż konto przez **Rejestrację** lub zaloguj się na istniejące konto.
3. Po zalogowaniu przejdź do **Dashboardu**, aby zarządzać portfelami i transakcjami.

Konto testowe administratora (tylko środowisko deweloperskie po `php artisan db:seed`):

| Pole | Wartość |
|------|---------|
| Email | `admin@capitex.pl` |
| Hasło | `admin` |

### Wymagania sprzętowe

Aplikacja działa w przeglądarce – obciążenie po stronie użytkownika jest niewielkie. Płynne korzystanie zapewni:

| Element | Zalecenie |
|---------|-----------|
| Urządzenie | Komputer, laptop, tablet lub smartfon |
| Procesor | Dowolny współczesny (np. Intel Core i3 / AMD Ryzen 3 lub nowszy) |
| Pamięć RAM | min. **4 GB** (zalecane 8 GB przy wielu kartach w przeglądarce) |
| Połączenie sieciowe | Stabilne łącze internetowe (pobieranie aktualnych cen z API zewnętrznych) |
| Przeglądarka | Aktualna wersja **Chrome**, **Firefox**, **Edge** lub **Safari** |
| Rozdzielczość | min. **360×640 px** (interfejs jest responsywny); wygodniej od **1280×720 px** na desktopie |

Na słabszym sprzęcie lub wolniejszym internecie aplikacja nadal działa, ale ładowanie cen aktywów i wykresów na dashboardzie może trwać nieco dłużej.

## Podręcznik użytkownika

> **Sekcja w przygotowaniu** – zostanie uzupełniona o ścieżki użytkownika, opisy funkcji, role w systemie oraz zrzuty ekranu z działającej aplikacji.

## Plany rozbudowy

### Czego zabrakło w pierwszej wersji (v1.0)

- **Transakcje sprzedaży** – system obsługuje wyłącznie transakcje typu *buy* (kupno); brak sprzedaży częściowej ani zamknięcia pozycji.
- **Edycja danych** – brak aktualizacji istniejących transakcji i portfeli (dostępne są: dodawanie i usuwanie).
- **Publiczne wdrożenie** – aplikacja działa lokalnie; nie ma jeszcze stałego adresu w internecie dla użytkownika końcowego.
- **Import / eksport** – brak importu historii z brokera (CSV, API XTB itd.) oraz eksportu raportów (PDF, Excel).
- **Powiadomienia** – brak alertów cenowych, e-maili ani powiadomień push.
- **Zaawansowane raporty** – brak raportów podatkowych, zestawień rocznych czy porównań z indeksami.
- **Wielowalutowość w UI** – kursy NBP są używane w obliczeniach, ale interfejs nie oferuje pełnego przełączania widoku na dowolną walutę.
- **Testy automatyczne** – brak pokrycia testami jednostkowymi i integracyjnymi kluczowych serwisów (np. `PortfolioService`, `ApiService`).

### Funkcjonalności na v2.0

- **Pełny cykl transakcji** – kupno i sprzedaż, automatyczne przeliczanie średniej ceny zakupu i realizowanego zysku/straty.
- **Import danych** – wgrywanie historii transakcji z pliku CSV lub integracja z wybranym brokerem.
- **Alerty i powiadomienia** – progi cenowe oraz e-maile (np. Laravel Notifications + kolejka zadań).
- **Raporty i eksport** – generowanie raportów PDF/Excel, wykresy historyczne wartości portfela w czasie.
- **Wdrożenie produkcyjne** – hosting z HTTPS, backup bazy, monitoring dostępności.
- **Uwierzytelnianie rozszerzone** – logowanie OAuth (Google), opcjonalnie 2FA.
- **API REST dla użytkownika** – odczyt portfela przez zewnętrzne narzędzia (z tokenami API).
- **Więcej klas aktywów** – obligacje, fundusze, nieruchomości (REIT) z dedykowanymi źródłami cen.

### Potencjał optymalizacji

- **Cache cen i kursów walut** – zapisywanie odpowiedzi z Binance, Yahoo Finance i NBP w Redis lub cache Laravela (np. 1–5 min), aby ograniczyć liczbę zapytań i przyspieszyć dashboard.
- **Kolejki zadań** – odświeżanie cen w tle (`php artisan queue:work`) zamiast synchronicznego pobierania przy każdym wejściu na stronę.
- **Baza danych** – migracja z SQLite na **PostgreSQL** lub **MySQL** przy większej liczbie użytkowników i potrzebie współbieżnych zapisów.
- **Indeksy i zapytania** – optymalizacja zapytań do transakcji (filtry, sortowanie) oraz indeksy na kolumnach `user_id`, `ticker`, `portfolio_id`.
- **Frontend** – lazy loading wykresów Chart.js, debounce w autocomplete aktywów, opcjonalnie PWA (działanie offline z cache statycznym).
- **CDN i asset pipeline** – serwowanie zbudowanych plików CSS/JS z CDN po wdrożeniu produkcyjnym.
- **Rate limiting API zewnętrznych** – centralne zarządzanie limitami zapytań do Yahoo/Binance, aby uniknąć blokad przy wielu użytkownikach jednocześnie.