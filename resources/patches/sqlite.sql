-- #!sqlite
-- #{ version
-- #  { 1.1.2
ALTER TABLE Pets ADD COLUMN Visible BOOL NOT NULL DEFAULT TRUE;
-- #  }
-- #}