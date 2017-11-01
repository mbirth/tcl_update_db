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

-- Needs SQLite 3.7 or newer
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

-- Needs SQLite 3.7 or newer
INSERT INTO "devices" VALUES
  ("PRD-63117-011", 1, "Unlocked EMEA"),
  ("PRD-63116-001", 0, "Unlocked USA")
;

-- we only care about the first file for now
-- a separate "files" table might get introduced later
CREATE TABLE "updates" (
  "updateId" INTEGER PRIMARY KEY AUTOINCREMENT,
  "tv" TEXT,       -- target version, e.g. AAQ302
  "fv" TEXT,       -- from version (only for OTA)
  "svn" TEXT,      -- version info from <SVN> field
  "pubDate" INTEGER,   -- published date
  "publisher" TEXT,    -- publisher
  "fwId" TEXT,     -- <FW_ID>
  "file_id" TEXT,      -- <FILE_ID> of first file
  "file_name" TEXT,    -- filename of first file
  "file_size" INTEGER, -- size of first file
  "file_sha1" TEXT,    -- SHA1 checksum of first file
  "type" TEXT,     -- FULL or OTA
  "note" TEXT,     -- some note for this file (optional)
);

-- Maps update files to devices
CREATE TABLE "update_map" (
  "deviceId" INTEGER REFERENCES "devices" ("deviceId"),
  "updateId" INTEGER REFERENCES "updates" ("updateId")
);
