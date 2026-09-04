SET NAMES utf8mb4;

CREATE TABLE users (
    id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name          VARCHAR(120) NOT NULL,
    email         VARCHAR(180) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uk_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE games (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    slug       VARCHAR(40) NOT NULL,
    name       VARCHAR(80) NOT NULL,
    sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    UNIQUE KEY uk_games_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE editions (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    game_id    INT UNSIGNED NOT NULL,
    code       VARCHAR(40) NOT NULL,
    name       VARCHAR(120) NOT NULL,
    sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    UNIQUE KEY uk_editions_game_code (game_id, code),
    CONSTRAINT fk_editions_game FOREIGN KEY (game_id) REFERENCES games (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE rarities (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    slug       VARCHAR(40) NOT NULL,
    name       VARCHAR(80) NOT NULL,
    sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    UNIQUE KEY uk_rarities_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE cards (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name_en    VARCHAR(180) NOT NULL,
    name_pt    VARCHAR(180) NULL,
    game_id    INT UNSIGNED NOT NULL,
    edition_id INT UNSIGNED NOT NULL,
    rarity_id  INT UNSIGNED NOT NULL,
    image_path VARCHAR(255) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_cards_game (game_id),
    KEY idx_cards_edition (edition_id),
    KEY idx_cards_rarity (rarity_id),
    KEY idx_cards_name_en (name_en),
    CONSTRAINT fk_cards_game    FOREIGN KEY (game_id)    REFERENCES games (id)     ON DELETE RESTRICT,
    CONSTRAINT fk_cards_edition FOREIGN KEY (edition_id) REFERENCES editions (id)  ON DELETE RESTRICT,
    CONSTRAINT fk_cards_rarity  FOREIGN KEY (rarity_id)  REFERENCES rarities (id)  ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
