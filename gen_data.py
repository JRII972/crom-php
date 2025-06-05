# Script de génération de données de test pour la base de données LBDR
import mysql.connector
import random
import uuid
from datetime import datetime, timedelta, date
import json

# Configuration de la connexion à la base de données
config = {
    'user': 'user',
    'password': 'userpassword',
    'host': 'db',
    'database': 'lbdr_db',
    'port': 3306
}

# Définition d'un dictionnaire pour associer les chemins d'images aux jeux correspondants
images_jeux = {
    "Warhammer": "https://cdn1.epicgames.com/offer/f640a0c1648147fea7e81565b45a3003/EGS_Warhammer40000SpaceMarine2_SaberInteractive_S1_2560x1440-975214651d1d1bc6c6e5779b53958840",
    "L'Appel de Cthulhu": "/data/images/creature-cthulhu-grand-ancien.webp",
    "Donjons et Dragons": "/data/images/donjon&dragon.jpg",
    "Vampire: La Mascarade": "/data/images/vampire_mascarde.avif",
    "Chroniques Oubliées": "/data/images/Chroniques_Oubliees.jpg",
    "Shadowrun": "/data/images/Shadowrun",
    "Numenera": "/data/images/Numenera.jpg",
    "Blades in the Dark": "/data/images/Blades in the Dark.jpg",
    "Cyberpunk RED": "/data/images/Cyberpunk RED.png",
    "Pathfinder": "/data/images/Pathfinder.jpg"
}

# Dictionnaire pour les icônes des jeux
icones_jeux = {
    "Warhammer": "/data/images/icon/Warhammer40k.png",
    "Donjons et Dragons": "/data/images/icon/dnd_logo_big.webp"
    # Les autres jeux n'ont pas d'icônes ou elles sont commentées dans le fichier games.tsx
}

try:
    # Connexion à la base de données
    conn = mysql.connector.connect(**config)
    cursor = conn.cursor()
    print("Connexion à la base de données réussie!")
    
    # --- FONCTION UTILITAIRES ---
    def generate_password_hash():
        """Génère un hash de mot de passe fictif"""
        return ''.join(random.choice('0123456789abcdef') for _ in range(64))
    
    def random_date(start_date, end_date):
        """Génère une date aléatoire entre start_date et end_date"""
        time_between_dates = end_date - start_date
        days_between_dates = time_between_dates.days
        random_number_of_days = random.randrange(days_between_dates)
        return start_date + timedelta(days=random_number_of_days)
    
    # --- GÉNÉRATION DES UTILISATEURS ---
    print("Génération des utilisateurs...")
    
    # Liste de prénoms et noms pour la génération
    prenoms = ['Alice', 'Bob', 'Charlie', 'David', 'Emma', 'François', 'Gilles', 'Hélène', 
               'Isabelle', 'Jean', 'Kevin', 'Lucie', 'Marc', 'Nathalie', 'Olivier', 'Patricia']
    noms = ['Martin', 'Dupont', 'Durand', 'Lefebvre', 'Moreau', 'Simon', 'Laurent', 
            'Michel', 'Leroy', 'Garcia', 'Bernard', 'Thomas', 'Robert', 'Richard']
    sexes = ['M', 'F', 'Autre']
    types_utilisateur = ['INSCRIT', 'INSCRIT', 'INSCRIT', 'ADMINISTRATEUR']  # Plus de chance d'avoir des inscrits

    # Génération d'utilisateurs
    users_data = []
    for i in range(20):
        user_id = str(uuid.uuid4())
        prenom = random.choice(prenoms)
        nom = random.choice(noms)
        login = f"{prenom.lower()}.{nom.lower()}{random.randint(1, 99)}"
        date_naissance = random_date(date(1970, 1, 1), date(2005, 12, 31))
        sexe = random.choice(sexes)
        id_discord = f"{prenom.lower()}#{random.randint(1000, 9999)}" if random.random() > 0.3 else None
        pseudonyme = f"{prenom.lower()}{random.randint(1, 99)}" if random.random() > 0.3 else None
        email = f"{login}@example.com" if random.random() > 0.2 else None
        mot_de_passe = generate_password_hash()
        image = None  # Pour simplifier
        type_utilisateur = random.choice(types_utilisateur)
        date_inscription = random_date(date(2020, 1, 1), date(2025, 5, 1))
        ancien_utilisateur = random.random() > 0.7
        premiere_connexion = random.random() > 0.8
        
        users_data.append((
            user_id, prenom, nom, login, date_naissance, sexe, id_discord, pseudonyme,
            email, mot_de_passe, image, type_utilisateur, date_inscription,
            ancien_utilisateur, premiere_connexion
        ))
    
    # Insertion des utilisateurs dans la base de données
    user_sql = """
    INSERT INTO utilisateurs (
        id, prenom, nom, login, date_de_naissance, sexe, id_discord, pseudonyme,
        email, mot_de_passe, image, type_utilisateur, date_inscription,
        ancien_utilisateur, premiere_connexion
    ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
    """
    
    cursor.executemany(user_sql, users_data)
    conn.commit()
    print(f"✅ {len(users_data)} utilisateurs créés avec succès!")
    

    # --- GÉNÉRATION DES JEUX ---
    print("Génération des jeux...")
    
    jeux_base = [
        # Jeux de rôle
        ("Donjons et Dragons", "Le jeu de rôle fantasy le plus populaire au monde", "JDR"),
        ("L'Appel de Cthulhu", "Jeu d'horreur lovecraftienne", "JDR"),
        ("Warhammer Fantasy", "Un monde de dark fantasy médiévale", "JDR"),
        ("Pathfinder", "Un jeu de rôle d'heroic fantasy", "JDR"),
        ("Vampire: La Mascarade", "Un jeu gothique-punk où vous incarnez des vampires", "JDR"),
        ("Cyberpunk RED", "Un jeu de rôle futuriste dans un monde dystopique", "JDR"),
        ("Star Wars: Edge of the Empire", "Aventures dans l'univers de Star Wars", "JDR"),
        ("Chroniques Oubliées", "Un jeu de rôle médiéval-fantastique accessible", "JDR"),
        ("Shadowrun", "Mélange de cyberpunk et de fantasy avec magie et technologie", "JDR"),
        ("Blades in the Dark", "Un jeu de criminels dans une cité victorienne hantée", "JDR"),
        ("Tales from the Loop", "Aventures d'enfants dans les années 80 alternatives", "JDR"),
        ("Loup-Garou l'Apocalypse", "Incarnez des guerriers-loups défenseurs de la nature", "JDR"),
        ("Le Seigneur des Anneaux JdR", "Aventures dans la Terre du Milieu", "JDR"),
        ("Alien RPG", "Horreur et survie dans l'espace", "JDR"),
        ("Numenera", "Science-fantasy dans un futur très lointain", "JDR"),
        
        # Jeux de société
        ("Catan", "Jeu de société de stratégie et de développement", "JEU_DE_SOCIETE"),
        ("7 Wonders", "Jeu de cartes et de civilisations", "JEU_DE_SOCIETE"),
        ("Pandemic", "Jeu coopératif pour sauver l'humanité", "JEU_DE_SOCIETE"),
        ("Terraforming Mars", "Jeu de stratégie sur la colonisation de Mars", "JEU_DE_SOCIETE"),
        ("Carcassonne", "Jeu de tuiles et de territoire", "JEU_DE_SOCIETE"),
        ("Dixit", "Jeu d'imagination et d'interprétation d'images", "JEU_DE_SOCIETE"),
        ("Splendor", "Jeu de développement économique dans la Renaissance", "JEU_DE_SOCIETE"),
        ("Les Aventuriers du Rail", "Construction de routes ferroviaires", "JEU_DE_SOCIETE"),
        ("Azul", "Jeu de placement de tuiles aux motifs inspirés d'azulejos", "JEU_DE_SOCIETE"),
        ("Codenames", "Jeu d'association de mots en équipes", "JEU_DE_SOCIETE"),
        ("King of Tokyo", "Affrontement de monstres géants", "JEU_DE_SOCIETE"),
        ("Dominion", "Jeu de construction de deck médiéval", "JEU_DE_SOCIETE"),
        ("Scythe", "Conquête de territoires dans un monde uchronique", "JEU_DE_SOCIETE"),
        ("Root", "Lutte asymétrique pour le contrôle de la forêt", "JEU_DE_SOCIETE"),
        ("Wingspan", "Collection d'oiseaux et création d'habitat", "JEU_DE_SOCIETE")
    ]
    
    # Préparation des données de jeux avec les images
    jeux_data = []
    for nom_jeu, description, type_jeu in jeux_base:
        # Chercher si le jeu a une image associée dans notre dictionnaire
        image = None
        
        # Gérer le cas de Warhammer (Warhammer Fantasy dans la base, mais Warhammer dans le dictionnaire d'images)
        recherche = "Warhammer" if nom_jeu.startswith("Warhammer") else nom_jeu
        
        # Pour Vampire, le nom exact est "Vampire: La Mascarade"
        if recherche == "Vampire: La Mascarade":
            image = images_jeux.get(recherche)
        else:
            # Recherche activitelle pour les autres jeux
            for cle in images_jeux:
                if cle in recherche:
                    image = images_jeux[cle]
                    break
        
        jeux_data.append((nom_jeu, description, type_jeu, image))
    
    jeux_sql = """
    INSERT INTO jeux (nom, description, type_jeu, image)
    VALUES (%s, %s, %s, %s)
    """
    
    cursor.executemany(jeux_sql, jeux_data)
    conn.commit()
    print(f"✅ {len(jeux_data)} jeux créés avec succès!")
    
    # --- OBTENTION DES IDS DES JEUX CRÉÉS ---
    cursor.execute("SELECT id, nom, image FROM jeux")
    jeux_info = cursor.fetchall()
    jeux_ids = [row[0] for row in jeux_info]
    
    # Créer un dictionnaire pour lier les ID des jeux à leurs images
    jeux_images_dict = {row[0]: row[2] for row in jeux_info}
    
    # --- GÉNÉRATION DES ASSOCIATIONS JEUX-GENRES ---
    print("Association des jeux aux genres...")
    
    # Récupération des ids de genres
    cursor.execute("SELECT id FROM genres")
    genres_ids = [row[0] for row in cursor.fetchall()]
    
    jeux_genres_data = []
    for jeu_id in jeux_ids:
        # Chaque jeu aura 1 à 3 genres aléatoires
        for _ in range(random.randint(1, 3)):
            genre_id = random.choice(genres_ids)
            jeux_genres_data.append((jeu_id, genre_id))
    
    # Éliminer les doublons
    jeux_genres_data = list(set(jeux_genres_data))
    
    jeux_genres_sql = """
    INSERT INTO jeux_genres (id_jeu, id_genre)
    VALUES (%s, %s)
    """
    
    cursor.executemany(jeux_genres_sql, jeux_genres_data)
    conn.commit()
    print(f"✅ {len(jeux_genres_data)} associations jeux-genres créées!")
    
    # --- GÉNÉRATION DES LIEUX ---
    print("Génération des lieux...")
    
    lieux_data = [
        ("Maison des Associations", "123 Rue des Jeux, 45000 Orléans", "Salle principale de l'association", 47.902964, 1.909251),
        ("FSV", "456 Avenue du Gaming, 45100 Orléans", "Fédération des Sports Virtuels", 47.913427, 1.873779),
        ("Discord", "En ligne", "Serveur Discord de l'association", None, None),
        ("Bibliothèque Municipale", "789 Boulevard des Livres, 45000 Orléans", "Salle de jeux au sous-sol", 47.899631, 1.900110),
        ("Café Ludique", "10 Place du Plateau, 45000 Orléans", "Café avec jeux de société", 47.905783, 1.899988),
        ("Centre Culturel", "42 Avenue des Arts, 45000 Orléans", "Grande salle pour événements", 47.895631, 1.904233),
        ("Salle Polyvalente", "15 Rue des Rencontres, 45100 Orléans", "Espace modulable pour grands groupes", 47.920154, 1.887542),
        ("Roll20", "En ligne", "Plateforme de jeu de rôle virtuelle", None, None),
        ("Bar Le D20", "27 Rue du Hasard, 45000 Orléans", "Bar à thème jeux de société", 47.908743, 1.912564),
        ("Local Associatif", "8 Impasse des Joueurs, 45000 Orléans", "Petit local de l'association", 47.903126, 1.906873)
    ]
    
    lieux_sql = """
    INSERT INTO lieux (nom, adresse, description, latitude, longitude)
    VALUES (%s, %s, %s, %s, %s)
    """
    
    cursor.executemany(lieux_sql, lieux_data)
    conn.commit()
    print(f"✅ {len(lieux_data)} lieux créés avec succès!")
    
    # --- OBTENTION DES IDS DES LIEUX CRÉÉS ---
    cursor.execute("SELECT id FROM lieux")
    lieux_ids = [row[0] for row in cursor.fetchall()]
    
    # --- OBTENTION DES IDS DES UTILISATEURS CRÉÉS ---
    cursor.execute("SELECT id FROM utilisateurs")
    utilisateurs_ids = [row[0] for row in cursor.fetchall()]
    
    # --- GÉNÉRATION DES PARTIES ---
    print("Génération des activites...")
    
    types_activite = ["CAMPAGNE", "ONESHOT", "JEU_DE_SOCIETE", "EVENEMENT"]
    types_campagne = ["OUVERTE", "FERMEE"]
    
    activites_data = []
    for i in range(15):
        nom = f"Activite {i+1}: L'aventure fantastique"
        id_jeu = random.choice(jeux_ids)
        id_maitre_jeu = random.choice(utilisateurs_ids)
        type_activite = random.choice(types_activite)
        type_campagne = random.choice(types_campagne) if type_activite == "CAMPAGNE" else None
        description_courte = f"Une aventure palpitante vous attend dans cette activite de jeu n°{i+1}!"
        description = f"Description complète de la activite {i+1}. Beaucoup de détails sur l'univers et les règles."
        nombre_max_joueurs = random.randint(3, 8)
        max_joueurs_session = min(nombre_max_joueurs, random.randint(2, 5))
        verrouille = random.random() > 0.8
        
        # Récupérer l'image du jeu associé pour l'utiliser comme image de la activite
        image = jeux_images_dict.get(id_jeu)
        texte_alt_image = f"Image pour la activite {nom}" if image else None
        
        activites_data.append((
            nom, id_jeu, id_maitre_jeu, type_activite, type_campagne,
            description_courte, description, nombre_max_joueurs,
            max_joueurs_session, verrouille, image, texte_alt_image
        ))
    
    activites_sql = """
    INSERT INTO activites (
        nom, id_jeu, id_maitre_jeu, type_activite, type_campagne,
        description_courte, description, nombre_max_joueurs,
        max_joueurs_session, verrouille, image, texte_alt_image
    ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
    """
    
    cursor.executemany(activites_sql, activites_data)
    conn.commit()
    print(f"✅ {len(activites_data)} activites créées avec succès!")
    
    # --- OBTENTION DES IDS DES PARTIES CRÉÉES ---
    cursor.execute("SELECT id FROM activites")
    activites_ids = [row[0] for row in cursor.fetchall()]
    
    # --- GÉNÉRATION DES SESSIONS ---
    print("Génération des sessions...")
    
    sessions_data = []
    for activite_id in activites_ids:
        # Chaque activite aura 1 à 5 sessions
        for j in range(random.randint(1, 5)):
            id_activite = activite_id
            id_lieu = random.choice(lieux_ids)
            # Les sessions sont programmées dans le futur
            date_session = (datetime.now() + timedelta(days=random.randint(1, 60))).strftime('%Y-%m-%d')
            heure_debut = f"{random.randint(18, 20)}:00:00"
            heure_fin = f"{random.randint(21, 23)}:00:00"
            
            # Récupérer le maître de jeu de la activite
            cursor.execute("SELECT id_maitre_jeu FROM activites WHERE id = %s", (id_activite,))
            id_maitre_jeu = cursor.fetchone()[0]
            
            # Récupérer max_joueurs_session de la activite
            cursor.execute("SELECT max_joueurs_session FROM activites WHERE id = %s", (id_activite,))
            max_joueurs_session = cursor.fetchone()[0]
            
            # Le nombre max de joueurs est souvent le même que celui de la activite, mais peut varier
            nombre_max_joueurs = max_joueurs_session if random.random() > 0.3 else random.randint(2, max_joueurs_session)
            
            sessions_data.append((
                id_activite, id_lieu, date_session, heure_debut, heure_fin,
                id_maitre_jeu, nombre_max_joueurs, max_joueurs_session
            ))
    
    sessions_sql = """
    INSERT INTO sessions (
        id_activite, id_lieu, date_session, heure_debut, heure_fin,
        id_maitre_jeu, nombre_max_joueurs, max_joueurs_session
    ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)
    """
    
    cursor.executemany(sessions_sql, sessions_data)
    conn.commit()
    print(f"✅ {len(sessions_data)} sessions créées avec succès!")
    
    # --- OBTENTION DES IDS DES SESSIONS CRÉÉES ---
    cursor.execute("SELECT id FROM sessions")
    sessions_ids = [row[0] for row in cursor.fetchall()]
    
    # --- GÉNÉRATION DES INSCRIPTIONS AUX SESSIONS ---
    print("Génération des inscriptions aux sessions...")
    
    joueurs_session_data = []
    for session_id in sessions_ids:
        # Récupérer le max_joueurs_session pour cette session
        cursor.execute("SELECT max_joueurs_session FROM sessions WHERE id = %s", (session_id,))
        max_joueurs = cursor.fetchone()[0]
        
        # Récupérer le maître de jeu de cette session pour ne pas l'inscrire comme joueur
        cursor.execute("SELECT id_maitre_jeu FROM sessions WHERE id = %s", (session_id,))
        id_maitre_jeu = cursor.fetchone()[0]
        
        # Chaque session aura un nombre aléatoire de joueurs inscrits
        nombre_joueurs = random.randint(0, max_joueurs)
        joueurs_potentiels = [u for u in utilisateurs_ids if u != id_maitre_jeu]
        joueurs_selectionnes = random.sample(joueurs_potentiels, min(nombre_joueurs, len(joueurs_potentiels)))
        
        for joueur_id in joueurs_selectionnes:
            date_inscription = datetime.now() - timedelta(days=random.randint(1, 30))
            joueurs_session_data.append((
                session_id, joueur_id, date_inscription.strftime('%Y-%m-%d %H:%M:%S')
            ))
    
    joueurs_session_sql = """
    INSERT INTO joueurs_session (
        id_session, id_utilisateur, date_inscription
    ) VALUES (%s, %s, %s)
    """
    
    cursor.executemany(joueurs_session_sql, joueurs_session_data)
    conn.commit()
    print(f"✅ {len(joueurs_session_data)} inscriptions aux sessions créées!")
    
    # --- GÉNÉRATION DES MEMBRES DE PARTIES FERMÉES ---
    print("Génération des membres de activites fermées...")
    
    membres_activite_data = []
    for activite_id in activites_ids:
        # Vérifier si c'est une campagne fermée
        cursor.execute("SELECT type_activite, type_campagne FROM activites WHERE id = %s", (activite_id,))
        result = cursor.fetchone()
        type_activite, type_campagne = result
        
        if type_activite == "CAMPAGNE" and type_campagne == "FERMEE":
            # Récupérer le maître de jeu de cette activite
            cursor.execute("SELECT id_maitre_jeu FROM activites WHERE id = %s", (activite_id,))
            id_maitre_jeu = cursor.fetchone()[0]
            
            # Récupérer le nombre max de joueurs pour cette activite
            cursor.execute("SELECT nombre_max_joueurs FROM activites WHERE id = %s", (activite_id,))
            max_joueurs = cursor.fetchone()[0]
            
            # Chaque activite fermée aura un nombre aléatoire de membres
            nombre_membres = random.randint(1, max_joueurs)
            membres_potentiels = [u for u in utilisateurs_ids if u != id_maitre_jeu]
            membres_selectionnes = random.sample(membres_potentiels, min(nombre_membres, len(membres_potentiels)))
            
            for membre_id in membres_selectionnes:
                membres_activite_data.append((activite_id, membre_id))
    
    membres_activite_sql = """
    INSERT INTO membres_activite (id_activite, id_utilisateur)
    VALUES (%s, %s)
    """
    
    cursor.executemany(membres_activite_sql, membres_activite_data)
    conn.commit()
    print(f"✅ {len(membres_activite_data)} membres de activites fermées créés!")
    
    print("\n✅ GÉNÉRATION DES DONNÉES TERMINÉE AVEC SUCCÈS! ✅")

except mysql.connector.Error as err:
    print(f"❌ Erreur: {err}")
finally:
    if 'conn' in locals() and conn.is_connected():
        cursor.close()
        conn.close()
        print("Connexion à la base de données fermée.")
