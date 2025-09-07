# local_padmap

Atomic pad assignment για Moodle 4.5.

## Λειτουργία
- Client: Rendezvous hashing → ταξινόμηση υποψήφιων pads ανά χρήστη.
- Server (`ajax.php`): atomic claim με unique index (username) → χωρίς συγκρούσεις στην πράξη.

## Εγκατάσταση
1. Αντέγραψε τον φάκελο `padmap` μέσα στο `local/`.
2. Πήγαινε στο Site administration → θα γίνει αυτόματα install του πίνακα.
3. Ρόλοι: το capability `local/padmap:view` δίνει πρόσβαση στη σελίδα λίστας.
4. Πρόσθεσε στο Generico template σου JS που κάνει POST στο `/local/padmap/ajax.php`.

## Endpoint
POST JSON στο `/local/padmap/ajax.php`:
```json
{ "action": "claim", "username": "jdoe", "pad": "https://...", "sesskey": "..." }
