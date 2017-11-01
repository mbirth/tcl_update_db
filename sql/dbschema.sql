PRAGMA journal_mode=WAL;
PRAGMA foreign_keys=on;

CREATE TABLE "families" (
  "familyId" INTEGER PRIMARY KEY AUTOINCREMENT,
  "name" TEXT   -- e.g. KEYone, Motion
);


-- Needs SQLite 3.7 or newer
INSERT INTO "families" ("name") VALUES
  ("KEYone"),
  ("Motion")
;

CREATE TABLE "models" (
  "modelId" INTEGER PRIMARY KEY AUTOINCREMENT,
  "familyId" INTEGER REFERENCES "families" ("familyId"),
  "name" TEXT   -- e.g. BBB100-1
);

INSERT INTO "models" VALUES
  (0, "BBB100-1"),
  (0, "BBB100-2"),
  (1, "BBD100-1")
;

CREATE TABLE "devices" (
  "deviceId" INTEGER PRIMARY KEY AUTOINCREMENT,
  "ref" TEXT,   -- PRD number
  "modelId" INTEGER REFERENCES "models" ("modelId"),
  "name" TEXT   -- e.g. Unlocked USA, Black KEYone
);

INSERT INTO "devices" VALUES
  ("PRD-63117-011", 1, "Unlocked EMEA"),
  ("PRD-63116-001", 0, "Unlocked USA")
;

CREATE TABLE "otas" (
  "otaId" INTEGER PRIMARY KEY AUTOINCREMENT,
  "fv" TEXT,     -- e.g. AAQ302
  "sha1" TEXT    -- SHA1 checksum of OTA file
);

