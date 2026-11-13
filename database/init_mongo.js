// Données de départ pour MongoDB (base v_et_g)
// Ce fichier s'exécute une seule fois : quand le volume mongo_data est vide.
// Il recopie les commandes de init.sql sous forme de documents pour les statistiques.

// Un numéro de commande ne peut exister qu'une seule fois (comme UNIQUE en SQL)
db.commandes.createIndex({ numero_commande: 1 }, { unique: true });

db.commandes.insertMany([
  {
    numero_commande: "CMD-2026-0001",
    menu_id: 1,
    menu_titre: "Burger Artisan",
    date_commande: new Date("2026-10-02T10:15:00Z"),
    nombre_personne: 6,
    prix_menu: 114.00,
    prix_total: 114.00,
    statut: "terminée"
  },
  {
    numero_commande: "CMD-2026-0002",
    menu_id: 3,
    menu_titre: "Tradition Française",
    date_commande: new Date("2026-10-20T14:30:00Z"),
    nombre_personne: 14,
    prix_menu: 327.60,
    prix_total: 339.68,
    statut: "en attente du retour de matériel"
  },
  {
    numero_commande: "CMD-2026-0003",
    menu_id: 4,
    menu_titre: "Fraîcheur Océane",
    date_commande: new Date("2026-11-05T09:00:00Z"),
    nombre_personne: 4,
    prix_menu: 88.00,
    prix_total: 88.00,
    statut: "en préparation"
  },
  {
    numero_commande: "CMD-2026-0004",
    menu_id: 6,
    menu_titre: "Evasion Italienne",
    date_commande: new Date("2026-11-10T18:45:00Z"),
    nombre_personne: 10,
    prix_menu: 210.00,
    prix_total: 222.08,
    statut: "en attente"
  },
  {
    numero_commande: "CMD-2026-0005",
    menu_id: 1,
    menu_titre: "Burger Artisan",
    date_commande: new Date("2026-09-05T11:00:00Z"),
    nombre_personne: 8,
    prix_menu: 152.00,
    prix_total: 152.00,
    statut: "terminée"
  },
  {
    numero_commande: "CMD-2026-0006",
    menu_id: 6,
    menu_titre: "Evasion Italienne",
    date_commande: new Date("2026-09-12T16:20:00Z"),
    nombre_personne: 8,
    prix_menu: 168.00,
    prix_total: 178.31,
    statut: "terminée"
  },
  {
    numero_commande: "CMD-2026-0007",
    menu_id: 3,
    menu_titre: "Tradition Française",
    date_commande: new Date("2026-09-25T10:00:00Z"),
    nombre_personne: 10,
    prix_menu: 260.00,
    prix_total: 260.00,
    statut: "terminée"
  },
  {
    numero_commande: "CMD-2026-0008",
    menu_id: 4,
    menu_titre: "Fraîcheur Océane",
    date_commande: new Date("2026-10-08T09:30:00Z"),
    nombre_personne: 9,
    prix_menu: 178.20,
    prix_total: 186.74,
    statut: "terminée"
  }
]);