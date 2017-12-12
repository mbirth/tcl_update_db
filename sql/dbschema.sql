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
INSERT INTO "models" ("familyId", "name") VALUES
  (1, "BBB100-1"),
  (1, "BBB100-2"),
  (2, "BBD100-1")
;

CREATE TABLE "devices" (
  "deviceId" INTEGER PRIMARY KEY AUTOINCREMENT,
  "ref" TEXT,   -- PRD number
  "modelId" INTEGER REFERENCES "models" ("modelId"),
  "name" TEXT   -- e.g. Unlocked USA, Black KEYone
);

-- Needs SQLite 3.7 or newer
INSERT INTO "devices" ("ref", "modelId", "name") VALUES
  ("PRD-63117-011", 2, "Unlocked EMEA"),
  ("PRD-63116-001", 1, "Unlocked USA")
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
  "svn" TEXT,      -- version info from <SVN> field
  "pubDate" INTEGER,   -- published date
  "publisher" TEXT,    -- publisher
  "fwId" TEXT,     -- <FW_ID> (CHANGES FOR THE SAME FILE_ID!!!) MAYBE MOVE TO update_map
  "file_id" TEXT,      -- <FILE_ID> of first file
  "file_sha1" TEXT REFERENCES "files" ("sha1")     -- SHA1 checksum of first file
);
CREATE UNIQUE INDEX "index_updates" ON "updates" (
  "fwId",
  "file_id"
);

-- Maps update files to devices
CREATE TABLE "update_map" (
  "deviceId" INTEGER REFERENCES "devices" ("deviceId"),
  "updateId" INTEGER REFERENCES "updates" ("updateId"),
  "seenDate" INTEGER   -- timestamp when this record was added
);
