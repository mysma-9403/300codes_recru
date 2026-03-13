# Library API - Zadanie rekrutacyjne 300.codes

API do zarządzania biblioteką (książki + autorzy) w Laravel 12 z architekturą DDD.

## Wymagania

- Docker + Docker Compose

## Instalacja i uruchomienie

```bash
make install
```

Komenda buduje kontenery, uruchamia migracje i seeduje bazę danymi testowymi.
Po zakończeniu wyświetlą się dane logowania.

### Inne komendy

| Komenda | Opis |
|---------|------|
| `make up` | Uruchom kontenery |
| `make down` | Zatrzymaj kontenery |
| `make fresh` | Zresetuj bazę i seeduj od nowa |
| `make test` | Uruchom testy (Pest) |
| `make pint` | Formatowanie kodu (Pint) |
| `make larastan` | Analiza statyczna (Larastan level 6) |
| `make analyse` | Pint + Larastan |
| `make shell` | Bash w kontenerze aplikacji |
| `make tinker` | Laravel Tinker |
| `make logs` | Logi kontenerów |

## Uwierzytelnianie (Sanctum)

Endpointy modyfikujące dane (`POST`, `PUT`, `DELETE`) wymagają tokenu Bearer.
Endpointy odczytujące (`GET`) są publiczne.

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

## Architektura

Projekt wykorzystuje Domain-Driven Design (DDD):

```
app/
├── Application/          # Warstwa aplikacji (Laravel)
│   ├── Auth/Controllers/
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
│   └── Shared/
│       └── Filters/
└── Console/Commands/
```

## Stack technologiczny

- PHP 8.3 + Laravel 12
- MySQL 8.0
- Redis (kolejki + cache)
- Docker + Docker Compose
- Pest (testy), Pint (formatowanie), Larastan (analiza statyczna)
- Sanctum (uwierzytelnianie API)

## Zrealizowane funkcjonalnosci

### Wymagane
- [x] Modele Book i Author z relacja wiele-do-wielu
- [x] CRUD dla ksiazek (z informacjami o autorach)
- [x] Lista i szczegoly autorow (z informacjami o ksiazkach)
- [x] Walidacja danych wejsciowych
- [x] Paginacja wynikow
- [x] Job kolejkowy aktualizujacy `last_added_book_title` u autorow
- [x] Testy dla `POST /api/books` oraz `DELETE /api/books/{id}`

### Dodatkowe
- [x] Sanctum - uwierzytelnianie dla endpointow modyfikujacych
- [x] Filtr `GET /api/authors?search={query}` - wyszukiwanie po tytulach ksiazek
- [x] Komenda Artisan `author:create` - interaktywne tworzenie autora
