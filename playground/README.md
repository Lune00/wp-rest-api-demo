# Playground PHP : Simulation origine consommant l'API

Un projet monté en local, servant d'origine d'un projet front qui viendra consommer l'API. Permet de tester la SOP, la politique de CORS définie côté API. Le projet est servi par le serveur intégré de PHP.

## Lancer

### Depuis la racine du projet

L'origine est définie par le protocole, le nom de domaine et le port. On peut donc remonter le projet sur différents ports pour simuler différentes origines.

`php -S localhost:8000 -t playground/`