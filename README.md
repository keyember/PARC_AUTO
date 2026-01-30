# Parc Auto

Application PHP de **dashboard** pour un parc automobile, affichant des statistiques globales et des listes synthétiques.

## 📋 Fonctionnalités

- Affichage du **total de véhicules** à partir de la table `vehicule`
- Affichage du **total de propriétaires** à partir de la table `personne`
- Calcul du **total des amendes** via la table `contravention`
- Affichage du **nombre total d'entretiens** via la table `entretien`
- Liste des **véhicules à risque** (coût d'entretien > 300 et amendes > 200)
- Liste des **contraventions récentes** (limitées à 4)
- Liste des **20 derniers entretiens**

## 🛠️ Stack technique

- **Langage** : PHP (procédural)
- **Base de données** : MySQL
- **Accès DB** : PDO avec prepared statements
- **Interface** : HTML/CSS

## 📂 Structure du projet
```
PARC_AUTO/
├── config/
│   ├── db.php
│   ├── generate_data.php
│   ├── seed.sql
│   └── .env
├── index.php
├── .gitignore
├── LICENSE
└── README.md
```
### Fichiers de configuration

**`config/db.php`** - Connexion PDO MySQL
- Charge les variables d'environnement depuis `.env`
- Établit la connexion à la base de données
- Gère les erreurs de connexion

**`config/.env`** - Variables d'environnement
- Paramètres de connexion MySQL
- À personnaliser pour votre environnement local

**`config/seed.sql`** - Schéma de base de données
- Crée les tables (`vehicule`, `personne`, `contravention`, `entretien`)
- Insère les données d'exemple

**`config/generate_data.php`** - Générateur de données
- Script PHP pour générer des données de test
- Peut être utilisé pour remplir la base de données

**`index.php`** - Page principale du dashboard
- Récupère les données des tables MySQL
- Affiche les statistiques et listes

## 🚀 Installation

### 1. Clonez le dépôt

git clone https://github.com/keyember/PARC_AUTO.git <br>
cd PARC_AUTO

### 2. Configurez la connexion MySQL

Créez un fichier `.env` dans le dossier `config/` avec vos paramètres de connexion MySQL :
```.env
DB_HOST="localhost"
DB_NAME="parc_auto"
DB_USER="VOTRE USERNAME (souvent root)"
DB_PASS="VOTRE MOT DE PASSE"
DB_CHARSET="utf8mb4"
```
### 3. Importez le schéma de base de données

Allez dans le fichier **seed.sql** et faite ```CTRL+A``` puis ```CTRL+Enter```, cela créera la base de données et les tables

### 5. Accédez au dashboard

Dans le terminal de VSCode :
```bash
php -S localhost:8000
```
Ouvrez votre navigateur puis accéder à : http://localhost:8000/

## 📊 Explications des requêtes SQL

### Total de véhicules

SELECT COUNT(*) FROM vehicule;

### Total de propriétaires

SELECT COUNT(*) FROM personne;

### Total des amendes

SELECT SUM(montant) FROM contravention;

### Nombre d'entretiens

SELECT COUNT(*) FROM entretien;

### Véhicules à risque

SELECT v.* FROM vehicule v
LEFT JOIN (
    SELECT id_vehicule, SUM(cout) as total_cout FROM entretien GROUP BY id_vehicule
) e ON v.id = e.id_vehicule
LEFT JOIN (
    SELECT id_vehicule, SUM(montant) as total_amendes FROM contravention GROUP BY id_vehicule
) c ON v.id = c.id_vehicule
WHERE COALESCE(e.total_cout, 0) > 300 
  AND COALESCE(c.total_amendes, 0) > 200;

### Contraventions récentes

SELECT c.*, v.marque, v.modele, p.nom 
FROM contravention c
JOIN vehicule v ON c.id_vehicule = v.id
JOIN personne p ON v.id_proprietaire = p.id
ORDER BY c.date DESC
LIMIT 4;

### 20 derniers entretiens

SELECT e.*, v.marque, v.modele, p.nom
FROM entretien e
JOIN vehicule v ON e.id_vehicule = v.id
JOIN personne p ON v.id_proprietaire = p.id
ORDER BY e.date DESC
LIMIT 20;

## 🔒 Sécurité

- **Prepared Statements** : Toutes les requêtes utilisent des prepared statements PDO pour éviter les injections SQL
- **Variables d'environnement** : Les identifiants sensibles sont stockés dans `.env` (à ajouter à `.gitignore`)
- **PDO Exceptions** : Les erreurs de base de données sont gérées avec try/catch

## 📝 Licence

Licence MIT - Voir le fichier `LICENSE`

---

**Développeur** : [keyember](https://github.com/keyember)

**Dernière mise à jour** : 30 janvier 2026
