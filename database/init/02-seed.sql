SET NAMES utf8mb4;

INSERT INTO users (name, email, password_hash) VALUES
    ('Administrador', 'admin@cartas.local',  '$2y$10$4D4sSXyQ.2IUDoTQ0zRFyuO1hl851WeUGrmO5MdSzlmU9Vfcqluxy'),
    ('Editor',        'editor@cartas.local', '$2y$10$Q1Wwk3AlZq5lhBCuip1XSeVqxmIS3lCwv6pBnFAAQg/teGCMeP9UC');

INSERT INTO games (slug, name, sort_order) VALUES
    ('magic',   'Magic: The Gathering', 1),
    ('pokemon', 'Pokémon',              2),
    ('yugioh',  'Yu-Gi-Oh!',            3);

INSERT INTO editions (game_id, code, name, sort_order) VALUES
    ((SELECT id FROM games WHERE slug = 'magic'),   'dom',   'Dominaria',                          1),
    ((SELECT id FROM games WHERE slug = 'magic'),   'war',   'War of the Spark',                   2),
    ((SELECT id FROM games WHERE slug = 'magic'),   'eld',   'Throne of Eldraine',                 3),
    ((SELECT id FROM games WHERE slug = 'magic'),   'hob',   'The Hobbit',                         4),
    ((SELECT id FROM games WHERE slug = 'magic'),   'msh',   'Marvel Super Heroes',                5),
    ((SELECT id FROM games WHERE slug = 'pokemon'), 'base1', 'Base Set',                           1),
    ((SELECT id FROM games WHERE slug = 'pokemon'), 'swsh1', 'Sword & Shield',                     2),
    ((SELECT id FROM games WHERE slug = 'pokemon'), 'sv1',   'Scarlet & Violet',                   3),
    ((SELECT id FROM games WHERE slug = 'pokemon'), '30c',   '30th Celebration',                   4),
    ((SELECT id FROM games WHERE slug = 'pokemon'), 'cri',   'Chaos Rising',                       5),
    ((SELECT id FROM games WHERE slug = 'yugioh'),  'lob',   'Legend of Blue Eyes White Dragon',   1),
    ((SELECT id FROM games WHERE slug = 'yugioh'),  'mrd',   'Metal Raiders',                      2),
    ((SELECT id FROM games WHERE slug = 'yugioh'),  'sdy',   'Starter Deck: Yugi',                 3),
    ((SELECT id FROM games WHERE slug = 'yugioh'),  'rotd',  'Rise of the Duelist',                4),
    ((SELECT id FROM games WHERE slug = 'yugioh'),  'blzd',  'Blazing Dominion',                   5);

INSERT INTO rarities (slug, name, sort_order) VALUES
    ('comum',       'Comum',        1),
    ('incomum',     'Incomum',      2),
    ('rara',        'Rara',         3),
    ('super-rara',  'Super Rara',   4),
    ('ultra-rara',  'Ultra Rara',   5),
    ('secreta',     'Secreta',      6),
    ('mitica',      'Mítica',       7),
    ('promocional', 'Promocional',  8);

INSERT INTO cards (name_en, name_pt, game_id, edition_id, rarity_id, image_path, created_at, updated_at) VALUES
    ('Serra Angel', 'Anjo de Serra',
        (SELECT id FROM games WHERE slug = 'magic'),
        (SELECT e.id FROM editions e JOIN games g ON g.id = e.game_id WHERE g.slug = 'magic' AND e.code = 'dom'),
        (SELECT id FROM rarities WHERE slug = 'incomum'),
        '/assets/img/cards/serra-angel.jpg',
        DATE_SUB(NOW(), INTERVAL 96 DAY), DATE_SUB(NOW(), INTERVAL 96 DAY)),
    ('Nicol Bolas, Dragon-God', 'Nicol Bolas, Deus-Dragão',
        (SELECT id FROM games WHERE slug = 'magic'),
        (SELECT e.id FROM editions e JOIN games g ON g.id = e.game_id WHERE g.slug = 'magic' AND e.code = 'war'),
        (SELECT id FROM rarities WHERE slug = 'mitica'),
        '/assets/img/cards/nicol-bolas-dragon-god.jpg',
        DATE_SUB(NOW(), INTERVAL 74 DAY), DATE_SUB(NOW(), INTERVAL 74 DAY)),
    ('Charizard', NULL,
        (SELECT id FROM games WHERE slug = 'pokemon'),
        (SELECT e.id FROM editions e JOIN games g ON g.id = e.game_id WHERE g.slug = 'pokemon' AND e.code = 'base1'),
        (SELECT id FROM rarities WHERE slug = 'rara'),
        '/assets/img/cards/charizard.jpg',
        DATE_SUB(NOW(), INTERVAL 51 DAY), DATE_SUB(NOW(), INTERVAL 51 DAY)),
    ('Pikachu', NULL,
        (SELECT id FROM games WHERE slug = 'pokemon'),
        (SELECT e.id FROM editions e JOIN games g ON g.id = e.game_id WHERE g.slug = 'pokemon' AND e.code = '30c'),
        (SELECT id FROM rarities WHERE slug = 'promocional'),
        '/assets/img/cards/pikachu.jpg',
        DATE_SUB(NOW(), INTERVAL 33 DAY), DATE_SUB(NOW(), INTERVAL 33 DAY)),
    ('Blue-Eyes White Dragon', 'Dragão Branco de Olhos Azuis',
        (SELECT id FROM games WHERE slug = 'yugioh'),
        (SELECT e.id FROM editions e JOIN games g ON g.id = e.game_id WHERE g.slug = 'yugioh' AND e.code = 'lob'),
        (SELECT id FROM rarities WHERE slug = 'ultra-rara'),
        '/assets/img/cards/blue-eyes-white-dragon.jpg',
        DATE_SUB(NOW(), INTERVAL 18 DAY), DATE_SUB(NOW(), INTERVAL 18 DAY)),
    ('Dark Magician', 'Mago Negro',
        (SELECT id FROM games WHERE slug = 'yugioh'),
        (SELECT e.id FROM editions e JOIN games g ON g.id = e.game_id WHERE g.slug = 'yugioh' AND e.code = 'sdy'),
        (SELECT id FROM rarities WHERE slug = 'super-rara'),
        '/assets/img/cards/dark-magician.jpg',
        DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 5 DAY));
