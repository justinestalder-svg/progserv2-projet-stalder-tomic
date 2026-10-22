<?php

// Classe qui gère la connexion à la base de données.
// Elle lit les identifiants dans config/database.ini
// et crée un objet PDO prêt à être utilisé.
class Database {
    // L'objet PDO (la connexion). private : seule la classe peut le modifier.
    private PDO $pdo;

    public function __construct() {
        // 1. Lire le fichier de configuration.
        // __DIR__ = le dossier de CE fichier (src/classes),
        // donc /../config/ remonte dans src/ puis entre dans config/.
        $config = parse_ini_file(__DIR__ . '/../config/database.ini');

        // 2. Construire le DSN : la "adresse" de la base pour PDO.
        // charset=utf8mb4 : pour que les accents s'affichent correctement.
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset=utf8mb4";

        // 3. Créer la connexion.
        $this->pdo = new PDO($dsn, $config['username'], $config['password']);

        // 4. fetch() et fetchAll() renverront des tableaux associatifs
        // (['libelle' => 'Pop']) au lieu de doublons avec des index numériques.
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    // Getter : permet aux pages d'utiliser la connexion.
    public function getPdo(): PDO {
        return $this->pdo;
    }
}
