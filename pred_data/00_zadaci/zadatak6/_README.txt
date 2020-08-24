Ova implementacija ne koristi MVC -- napišite i varijantu koja koristi.

Da bi aplikacija ispravno radila, na web-serveru mora raditi PHP funkcija mail.
To je slučaj na rp2-serveru, ali lako moguće da nije na vašem računalu doma.
Zato je najbolje testirati ovu aplikaciju na rp2-serveru.

Prije pokretanja aplikacije treba:
- ispraviti podatke za spajanje na bazu podataka u db.class.php
- u bazi treba napraviti tablicu UserList sa stupcima username, password, email, reg_seq i has_registered. Sve su VARCHAR osim zadnjeg koji je INT.
