# ⏱️ WorkTimeSystemAPI

API do rejestracji oraz podsumowywania czasu pracy pracowników. Projekt oparty na frameworku Symfony.

---

## Spis treści

- [Funkcjonalności](#funkcjonalności)
- [Wymagania](#wymagania)
- [Instalacja](#instalacja)
- [Endpointy](#endpointy)
- [Walidacja i błędy](#walidacja-i-błędy)
- [Testy](#testy)

---

## ✨ Funkcjonalności

- Utworzenie nowego pracownika
- Rejestrowanie czasu pracy z walidacją (kolejność dat, maks. 12h, jedna rejestracja / dzień)
- Podsumowanie godzin i wynagrodzenia z konkretnego dnia
- Podsumowanie godzin i wynagrodzenia z całego miesiąca (w tym nadgodziny)
- Obsługa walidacji i wyjątków z tłumaczeniami
- REST API w formacie JSON z odpowiednimi statusami kodu
- Gotowa konfiguracja Docker Compose z bazą danych MariaDB i Adminerem

## 📦 Wymagania

- PHP 8.2.12 lub nowszy
- Symfony 7.2.6
- Composer
- Baza danych MariaDB 11.7.2
- Docker (opcjonalnie - do lokalnej bazy danych)

## ⚙️ Instalacja

1. **Sklonuj repozytorium:**

```bash
git clone https://github.com/mateusz-przybyla/WorkTimeSystemAPI.git
cd WorkTimeSystemAPI
```

2. **Zainstaluj zależności:**

```bash
composer install
```

3. **Skonfiguruj .env**

Domyślnie dane: 

```bash
DATABASE_URL="mysql://mateusz:mateusz@127.0.0.1:3306/work_time_system_api_db?serverVersion=mariadb-11.7.2&charset=utf8mb4"
```

4. **Uruchom bazę danych z Dockerem:**

Możesz skorzystać z gotowego pliku `compose.yaml`, który uruchamia kontener bazy danych oraz Adminera.

```bash
docker compose up -d
```

- MariaDB 11.7.2 (na porcie `3306`)
- Adminer (GUI do zarządzania DB – dostępny na `http://localhost:8080`)

5. **Utwórz schemat bazy danych:**

```bash
symfony console doctrine:migrations:migrate
```

6. **Uruchom lokalny serwer Symfony:**

```bash
symfony server:start
```

## 🛠️ Endpointy

### 🏠 Strona powitania

**GET `/`**

### 📌 Utworzenie pracownika

**POST `/api/employee`**

**Przykładowe żądanie:**

```json
{
  "firstname": "Jan",
  "surname": "Kowalski"
}
```

**Przykładowa odpowiedź:**

```json
{
  "response": {
    "uuid": "a29078ae-7f68-403c-9b1f-bd9b3e02f4d7"
  }
}
```
Status kodu: `201 Created`

**Możliwe błędy:**

```json
{
  "error": {
    "firstname": [
      "Imię jest wymagane."
    ],
    "surname": [
      "Nazwisko jest wymagane."
    ]
  }
}
```
Status kodu: `422 Unprocessable Entity`

```json
{
  "error": "Nieprawidłowy format JSON."
}
```
Status kodu: `400 Bad Request`

### 🕒 Rejestracja czasu pracy

**POST `/api/work-time`**

**Przykładowe żądanie:**

```json
{
  "uuid": "a29078ae-7f68-403c-9b1f-bd9b3e02f4d7",
  "startTime": "20.05.2024 08:00",
  "endTime": "20.05.2024 16:00"
}
```

**Przykładowa odpowiedź:**

```json
{
  "response": [
    "Czas pracy został dodany."
  ]
}
```
Status kodu: `201 Created`

**Możliwe błędy:**

```json
{
  "error": [
    "Data zakończenia musi być po dacie rozpoczęcia.",
    "Czas pracy nie może przekraczać 12 godzin."
  ]
}
```
Status kodu: `422 Unprocessable Entity`

```json
{
  "error": [
    "Nie znaleziono pracownika."
  ]
}
```
Status kodu: `404 Not Found`

```json
{
  "error": [
    "Pracownik już posiada zarejestrowany czas pracy w tym dniu."
  ]
}
```
Status kodu: `409 Conflict`

### 📊 Podsumowanie dnia

**POST `/api/summary/day`**

**Przykładowe żądanie:**

```json
{
  "uuid": "a29078ae-7f68-403c-9b1f-bd9b3e02f4d7",
  "date": "20.05.2024"
}
```

**Przykładowa odpowiedź:**

```json
{
  "response": {
    "suma po przeliczeniu": "160 PLN",
    "ilość godzin z danego dnia": 8,
    "stawka": "20 PLN"
  }
}
```
Status kodu: `200 OK`

**Możliwe błędy:**

```json
{
  "error": [
    "Nie znaleziono pracownika."
  ]
}
```
Status kodu: `404 Not Found`

### 📆 Podsumowanie miesiąca

**POST `/api/summary/month`**

**Przykładowe żądanie:**

```json
{
  "uuid": "a29078ae-7f68-403c-9b1f-bd9b3e02f4d7",
  "date": "05.2024"
}
```

**Przykładowa odpowiedź:**

```json
{
  "response": {
    "ilość normalnych godzin z danego miesiąca": 40,
    "stawka": "20 PLN",
    "ilość nadgodzin z danego miesiąca": 5,
    "stawka nadgodzinowa": "40 PLN",
    "suma po przeliczeniu": "1100 PLN"
  }
}
```
Status kodu: `200 OK`

**Możliwe błędy:**

```json
{
  "error": [
    "Nie znaleziono pracownika."
  ]
}
```
Status kodu: `404 Not Found`

---

## Walidacja i błędy

Błędy walidacyjne i biznesowe są tłumaczone za pomocą komponentu translatora. Przykład błędu:

```json
{
  "error": [
    "Czas pracy nie może przekraczać 12 godzin."
  ]
}
```

Wszystkie wyjątki HTTP (np. `NotFoundHttpException`, `ConflictHttpException`) są przechwytywane przez `ApiExceptionListener`, który zwraca odpowiedź w formacie JSON.

---

## 🧪 Testy

Testy jednostkowe uruchomisz za pomocą PHPUnit:

```bash
php bin/phpunit
```

Testowany jest m.in. serwis `WorkTimeService`, jego logika rozliczania i podsumowania czasu pracy.