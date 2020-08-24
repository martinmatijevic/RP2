# Projekt za kolegij Računarski praktikum 2
Tema: Kolaborativni editor teksta

## Baza podataka:
Tablica doc_users s atributima first_name, last_name, username PK, password, email, documents

Tablica documents s atributima id PK, title, creator_username, content, last_edit_time, last_edit_user

Tablica Collaborators je veza između doc_users i documents, atributi su username PK, id PK

Tablica Messages s atributima id PK, username, document_id, time, content

## Login page:
standardno, nudi se i mogućnost registracije

## Home page:
prikaz svih dokumenata (title i id dokumenta) na kojima user radi (može ih otvoriti ili obrisati sa svoje liste - da više ne sudjeluje u njima)

gumb za kreiranje novog dokumenta

gumb za logout

## Kreiranje novog dokumenta:
user stvara novi dokument s home page-a -> izbacuje se skočni prozor u koji upisuje title -> kreira se dokument s tim title-om i user-a se prebaci na view za uređivanje dokumenta

## Uređivanje dokumenta:
u početku je jedini user collaborator onaj user koji je kreirao dokument

svaki user collaborator može dodavati druge collaboratore, pomoću username-a

svaki user collaborator može promijeniti naziv dokumenta (promjena se zabilježi kao poruka u chatu, autor poruke može biti systemmessage ili changes ili tako nešto) - postoji gumb za to

gumb za vraćanje na home page

samo useru koji je kreirao dokument se prikazuje mogućnost trajnog brisanja dokumenta

sa strane se prikazuje chat

dokument se u svakom trenutku može downloadati u odgovarajućem formatu

## Chat:
pamti sve prethodne poruke s datumom i username-om autora
