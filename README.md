# FoodTinder – Backend

> Laravel API do tindera dla dań

---

## Spis treści
- [Informacje ogólne](#informacje-ogólne)
- [Wymagania wstępne](#wymagania-wstępne)
- [Szybki start](#szybki-start)
- [Konfiguracja bazy danych](#konfiguracja-bazy-danych)
- [Konta testowe](#konta-testowe)
- [Pobranie przykładowych zdjęć](#pobranie-przykładowych-zdjęć)
- [Dokumentacja API (Swagger)](#dokumentacja-api-swagger)
- [Autoryzacja i testowanie API](#autoryzacja-i-testowanie-api)
- [Przydatne komendy](#przydatne-komendy)
- [Rozwiązywanie problemów](#rozwiązywanie-problemów)

---

## Informacje ogólne

- **PHP**: `^8.2`
- **Framework**: Laravel
- **Autoryzacja**: Laravel Sanctum
- **Dokumentacja API**: L5-Swagger

---

## Wymagania wstępne

- PHP `^8.2` (z rozszerzeniami: `pdo`, `mbstring`, `openssl`, `json`, `ctype`, `fileinfo`, `tokenizer`, `xml`, `curl`)
- Composer
- MariaDB/MySQL (na Windows polecany **XAMPP**; na Linux: pakiet **mariadb** + **httpd/nginx**)
- (Opcjonalnie) Git Bash lub WSL na Windows (do uruchomienia skryptu `curl.sh`), ale powinien działać na powershellu, jeżeli nie to najlepiej git bash

---

## Szybki start

Wygeneruj klucz aplikacji:

```bash
php artisan key:generate
```

Zainstaluj zależności i odpal projekt:

```bash
composer install
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve
```

Domyślnie aplikacja będzie dostępna pod: **http://127.0.0.1:8000**

---

## Konfiguracja bazy danych

Utwórz bazę danych o nazwie **`foodtinder`** w MariaDB/MySQL


> Uwaga: Dostosuj `DB_USERNAME` i `DB_PASSWORD`, które są w .env do swojej instalacji mariadb.

---

## Konta testowe

Standardowe konto (rola admin/user zgodnie z seedami):

- **Email:** `test@example.com`
- **Hasło:** `password`

---

## Pobranie przykładowych zdjęć

### Linux / macOS
```bash
cd storage/app/public
chmod +x curl.sh
./curl.sh
```

### Windows (PowerShell)
Masz kilka opcji:
- **Git Bash**: uruchom `curl.sh` w Git Bash
- **WSL**: w PowerShellu:
  ```powershell
  cd storage/app/public
  wsl ./curl.sh
  ```
- **PowerShell** (jeśli masz `bash` w PATH):
  ```powershell
  cd storage/app/public
  bash curl.sh
  ```

---

## Dokumentacja API (Swagger)

Wygeneruj dokumentację:

```bash
php artisan l5-swagger:generate
```

Wejdź na:
- **UI**: `http://127.0.0.1:8000/api/documentation`

> Jeśli UI nie działa, wyczyść cache (patrz sekcja *Rozwiązywanie problemów*) i upewnij się, że pliki swaggera są generowane do `storage/api-docs`.

---

## Autoryzacja i testowanie API

- Uzyskaj token: `POST /api/login` z danymi konta testowego
- Używaj **Bearer Token** w nagłówku:
  ```
  Authorization: Bearer <TOKEN>
  ```

Polecane narzędzia:
- **Postman** (API)
- **Burp Suite** (API)
- **Curl w terminalu** (API, ale dla masochistów)

---

## Przydatne komendy

**Generowanie PHPDoc dla modeli (IDE-helper):**
```bash
php artisan ide-helper:models -RW
```

**Generowanie dokumentacji OpenAPI:**
```bash
php artisan l5-swagger:generate
```

**Reset bazy + seed:**
```bash
php artisan migrate:fresh --seed
```

**Link do storage (pliki publiczne):**
```bash
php artisan storage:link
```

**Czyszczenie cache’u:**
```bash
php artisan optimize:clear
```

**Uruchomienie testów:**
```bash
php artisan test
```

---

## Rozwiązywanie problemów

- **Błąd 500 po starcie**  
  Upewnij się, że wykonałeś:
  ```bash
  php artisan key:generate
  php artisan migrate:fresh --seed
  php artisan storage:link
  php artisan optimize:clear
  ```
- **Swagger nie widzi nowych schematów/annotacji**
    1) Sprawdź, czy pliki z annotacjami są w katalogach skanowanych przez L5-Swagger (konfig: `config/l5-swagger.php`, klucz `paths.annotations`)
    2) Wygeneruj ponownie:
       ```bash
       php artisan l5-swagger:generate
       php artisan optimize:clear
       ```
- **Brak uprawnień do zapisu w `storage`/`bootstrap/cache`**  
  Ustaw prawa:
  ```bash
  chmod -R 775 storage bootstrap/cache
  ```
- **Nie działa `curl.sh` na Windows**  
  Użyj Git Bash, WSL, powershell (patrz sekcja *Pobranie przykładowych zdjęć*).
- **Błędy migracji po zmianach w schemacie**  
  Wykonaj:
  ```bash
  php artisan migrate:fresh --seed
  ```

---

#### Życzę miłej zabawy z Food Tinder :D

---

#### Autor: Jacek Piotrowski
