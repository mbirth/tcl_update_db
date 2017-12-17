PRAGMA journal_mode=WAL;
PRAGMA foreign_keys=on;

CREATE TABLE "families" (
  "familyId" INTEGER PRIMARY KEY AUTOINCREMENT,
  "name" TEXT   -- e.g. KEYone, Motion
);

-- Needs SQLite 3.7 or newer
INSERT INTO "families" ("name") VALUES
  ("KEYone"),   -- familyId 1
  ("Motion")    -- familyId 2
;

CREATE TABLE "models" (
  "modelId" INTEGER PRIMARY KEY AUTOINCREMENT,
  "familyId" INTEGER REFERENCES "families" ("familyId"),
  "name" TEXT   -- e.g. BBB100-1
);

-- Needs SQLite 3.7 or newer
INSERT INTO "models" ("familyId", "name") VALUES
  (1, "BBB100-1"),   -- modelId 1
  (1, "BBB100-2"),   -- modelId 2
  (1, "BBB100-3"),   -- modelId 3
  (1, "BBB100-4/-5"),   -- modelId 4
  (1, "BBB100-6"),   -- modelId 5
  (1, "BBB100-7"),   -- modelId 6
  (2, "BBD100-1"),   -- modelId 7
  (2, "BBD100-2"),   -- modelId 8
  (2, "BBD100-6")    -- modelId 9
;

CREATE TABLE "devices" (
  "deviceId" INTEGER PRIMARY KEY AUTOINCREMENT,
  "ref" TEXT,   -- PRD number
  "modelId" INTEGER REFERENCES "models" ("modelId"),
  "name" TEXT   -- e.g. Unlocked USA, Black KEYone
);

-- Needs SQLite 3.7 or newer
INSERT INTO "devices" ("ref", "modelId", "name") VALUES
  ("PRD-63116-001", 1, "Unlocked USA"),
  ("PRD-63116-003", 1, "Bell"),
  ("PRD-63116-005", 1, "Rogers"),
  ("PRD-63116-007", 1, "Telus"),
  ("PRD-63116-009", 1, "Hong-Kong"),
  ("PRD-63116-010", 1, ""),
  ("PRD-63116-013", 1, ""),
  ("PRD-63116-017", 1, ""),
  ("PRD-63116-020", 1, ""),
  ("PRD-63116-021", 1, "HK?"),
  ("PRD-63116-023", 1, ""),
  ("PRD-63116-024", 1, ""),
  ("PRD-63116-027", 1, ""),
  ("PRD-63116-029", 1, ""),
  ("PRD-63116-033", 1, ""),
  ("PRD-63116-036", 1, "AT&T"),
  ("PRD-63116-039", 1, ""),
  ("PRD-63116-040", 1, ""),
  ("PRD-63116-041", 1, "Black KEYone"),
  ("PRD-63116-042", 1, "Black KEYone"),
  ("PRD-63116-043", 1, "Black KEYone"),
  ("PRD-63116-044", 1, ""),
  ("PRD-63116-047", 1, "Black KEYone"),
  ("PRD-63116-051", 1, "Black KEYone"),
  ("PRD-63116-055", 1, "Black KEYone"),
  ("PRD-63117-003", 2, "Unlocked UK"),
  ("PRD-63117-011", 2, "Unlocked EMEA"),
  ("PRD-63117-015", 2, "NL, Belgium"),
  ("PRD-63117-017", 2, ""),
  ("PRD-63117-019", 2, ""),
  ("PRD-63117-021", 2, ""),
  ("PRD-63117-023", 2, "AZERTY Belgium"),
  ("PRD-63117-025", 2, ""),
  ("PRD-63117-027", 2, "QWERTY UAE"),
  ("PRD-63117-028", 2, ""),
  ("PRD-63117-029", 2, ""),
  ("PRD-63117-034", 2, "UAE?"),
  ("PRD-63117-035", 2, ""),
  ("PRD-63117-036", 2, ""),
  ("PRD-63117-037", 2, ""),
  ("PRD-63117-039", 2, ""),
  ("PRD-63117-040", 2, ""),
  ("PRD-63117-041", 2, ""),
  ("PRD-63117-042", 2, ""),
  ("PRD-63117-043", 2, ""),
  ("PRD-63117-044", 2, ""),
  ("PRD-63117-047", 2, ""),
  ("PRD-63117-703", 2, "Prerelease"),
  ("PRD-63117-704", 2, "Prerelease"),
  ("PRD-63117-717", 2, "Prerelease Black KEYone"),
  ("PRD-63118-001", 3, "Unlocked"),
  ("PRD-63118-003", 3, "Sprint"),
  ("PRD-63734-001", 4, "Unlocked -4"),
  ("PRD-63734-002", 4, "Unlocked -4"),
  ("PRD-63734-003", 4, "Unlocked -5"),
  ("PRD-63734-004", 4, "Unlocked -5"),
  ("PRD-63763-001", 5, "Unlocked"),
  ("PRD-63763-002", 5, "Unlocked Black KEYone"),
  ("PRD-63764-001", 6, "Unlocked"),
  ("PRD-63737-003", 7, ""),
  ("PRD-63737-007", 7, ""),
  ("PRD-63737-009", 7, ""),
  ("PRD-63739-009", 8, ""),
  ("PRD-63739-010", 8, ""),
  ("PRD-63753-002", 9, ""),
  ("PRD-63753-003", 9, "")
;

CREATE TABLE "files" (
  "sha1" TEXT UNIQUE PRIMARY KEY,   -- checksum of file
  "file_name" TEXT,      -- filename of file
  "file_size" INTEGER,   -- size
  "type" TEXT,           -- FULL(4) or OTA(2) update
  "fv" TEXT,             -- from version (only for OTA)
  "tv" TEXT,             -- target version, e.g. AAQ302
  "note" TEXT,           -- description of file (optional)
  "published_first" INTEGER,   -- stamp of earliest pubdate
  "published_last" INTEGER     -- stamp of latest pubdate
);

-- we only care about the first file for now
-- a separate "files" table might get introduced later
CREATE TABLE "updates" (
  "updateId" INTEGER PRIMARY KEY AUTOINCREMENT,
  "curef" TEXT,    -- PRD number
  "update_desc" TEXT,
  "svn" TEXT,      -- version info from <SVN> field
  "seenDate" INTEGER,  -- date added to db
  "pubDate" INTEGER,   -- published date
  "publisher" TEXT,    -- publisher
  "num_files" INTEGER, -- number of files total
  "fwId" TEXT,     -- <FW_ID> (CHANGES FOR THE SAME FILE_ID!!!) MAYBE MOVE TO update_map
  "file_id" TEXT,      -- <FILE_ID> of first file
  "file_sha1" TEXT REFERENCES "files" ("sha1")     -- SHA1 checksum of first file
);
CREATE UNIQUE INDEX "index_updates" ON "updates" (
  "curef",
  "fwId",
  "file_id"
);
