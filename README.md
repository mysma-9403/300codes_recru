# Library API - Zadanie rekrutacyjne 300.codes

API do zarządzania biblioteką (książki + autorzy) w Laravel 12 z architekturą DDD.

## Wymagania

- Docker + Docker Compose

## Instalacja i uruchomienie

```bash
make install
```

Komenda automatycznie:
1. Tworzy `.env` z `.env.example` (jesli nie istnieje)
2. Buduje i uruchamia kontenery (app, mysql, redis, queue worker)
3. Generuje APP_KEY
4. Uruchamia migracje i seeduje baze danymi testowymi
5. Wyswietla dane logowania

Jesli porty sa zajete, zmien je w `docker-compose.override.yml`.

### Inne komendy

| Komenda | Opis |
|---------|------|
| `make up` | Uruchom kontenery |
| `make down` | Zatrzymaj kontenery |
| `make restart` | Restartuj kontenery |
| `make fresh` | Zresetuj baze i seeduj od nowa |
| `make test` | Uruchom testy (Pest) |
| `make pint` | Formatowanie kodu (Pint) |
| `make larastan` | Analiza statyczna (Larastan level 6) |
| `make analyse` | Pint + Larastan |
| `make shell` | Bash w kontenerze aplikacji |
| `make tinker` | Laravel Tinker |
| `make logs` | Logi kontenerow |

## Uwierzytelnianie (Sanctum)

Endpointy modyfikujace dane (`POST`, `PUT`, `DELETE /books`) wymagaja tokenu Bearer.
Endpointy odczytujace (`GET`) sa publiczne.

Wszystkie odpowiedzi API sa w formacie JSON (middleware `ForceJsonResponse`).

### Jak uzyskac token?

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email": "test@example.com", "password": "password"}'
```

Odpowiedz:
```json
{"token": "1|abc123..."}
```

### Jak uzywac tokenu?

Dodaj naglowek `Authorization` do zapytan:
```bash
curl -X POST http://localhost:8000/api/books \
  -H "Authorization: Bearer 1|abc123..." \
  -H "Content-Type: application/json" \
  -d '{"title": "Harry Potter", "isbn": "9781234567890", "author_ids": [1]}'
```

## Endpointy API

Bazowy URL: `http://localhost:8000/api`

### Ksiazki

| Metoda | Endpoint | Opis | Autoryzacja |
|--------|----------|------|-------------|
| `GET` | `/books` | Lista ksiazek z autorami (paginacja) | Nie |
| `GET` | `/books/{id}` | Szczegoly ksiazki z autorami | Nie |
| `POST` | `/books` | Dodaj nowa ksiazke | Tak |
| `PUT` | `/books/{id}` | Aktualizuj ksiazke | Tak |
| `DELETE` | `/books/{id}` | Usun ksiazke | Tak |

#### Przyklad body dla POST/PUT `/books`:
```json
{
  "title": "Harry Potter i Kamien Filozoficzny",
  "description": "Pierwsza czesc serii",
  "isbn": "9781234567890",
  "published_year": 1997,
  "author_ids": [1, 2]
}
```

Tytuly ksiazek sa automatycznie normalizowane (np. `harry potter` -> `Harry Potter`).

### Autorzy

| Metoda | Endpoint | Opis | Autoryzacja |
|--------|----------|------|-------------|
| `GET` | `/authors` | Lista autorow z ksiazkami (paginacja) | Nie |
| `GET` | `/authors/{id}` | Szczegoly autora z ksiazkami | Nie |

#### Filtrowanie autorow

Wyszukiwanie autorow, ktorzy maja ksiazki zawierajace dany ciag znakow w tytule:
```
GET /api/authors?search=potter
```

System filtrow jest rozszerzalny — dodanie nowego filtra wymaga jedynie stworzenia klasy implementujacej `FilterInterface` i zarejestrowania jej w `AuthorFilterPipeline`.

### Logowanie

| Metoda | Endpoint | Opis |
|--------|----------|------|
| `POST` | `/login` | Logowanie, zwraca token Sanctum |

## Komenda Artisan

Tworzenie nowego autora interaktywnie:
```bash
make shell
php artisan author:create
```

## Testy

```bash
make test
```

Projekt zawiera 23 testy (105 asercji):

- **Feature/Book/** — testy jednostkowe POST i DELETE ksiazek (walidacja, autoryzacja, dispatch joba)
- **Feature/E2E/** — testy end-to-end:
  - `BookFlowTest` — pelny cykl CRUD ksiazki z tokenem Sanctum, paginacja
  - `AuthorFlowTest` — lista, szczegoly, filtr search, 404
  - `AuthorizationFlowTest` — publiczne vs chronione endpointy, nieprawidlowy token
  - `LastBookTitleJobTest` — aktualizacja `last_added_book_title` u autorow

## Architektura

Projekt wykorzystuje Domain-Driven Design (DDD):

```
app/
├── Application/          # Warstwa aplikacji (Laravel)
│   ├── Auth/
│   │   ├── Controllers/
│   │   ├── Requests/
│   │   └── Services/
│   ├── Author/
│   │   ├── Controllers/
│   │   └── Resources/
│   └── Book/
│       ├── Controllers/
│       ├── Jobs/
│       ├── Requests/
│       └── Resources/
├── Domain/               # Warstwa domenowa (logika biznesowa)
│   ├── Author/
│   │   ├── DataTransferObjects/
│   │   ├── Factories/
│   │   ├── Filters/
│   │   ├── Models/
│   │   └── Services/
│   ├── Book/
│   │   ├── DataTransferObjects/
│   │   ├── Factories/
│   │   ├── Models/
│   │   └── Services/
│   ├── Shared/
│   │   └── Filters/
│   └── User/
│       └── Services/
├── Console/Commands/
└── Http/Middleware/
```

## Stack technologiczny

- PHP 8.3 + Laravel 12
- MySQL 8.0
- Redis (kolejki + cache)
- Docker + Docker Compose
- Pest (testy), Pint (formatowanie), Larastan level 6 (analiza statyczna)
- Sanctum (uwierzytelnianie API)
