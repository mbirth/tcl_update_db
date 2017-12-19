PRAGMA journal_mode=WAL;
PRAGMA foreign_keys=on;

CREATE TABLE "families" (
  "familyId" INTEGER PRIMARY KEY AUTOINCREMENT,
  "name" TEXT UNIQUE   -- e.g. KEYone, Motion
);


CREATE TABLE "models" (
  "modelId" INTEGER PRIMARY KEY AUTOINCREMENT,
  "familyId" INTEGER REFERENCES "families" ("familyId"),
  "name" TEXT UNIQUE   -- e.g. BBB100-1
);

CREATE TABLE "devices" (
  "curef" TEXT UNIQUE PRIMARY KEY,   -- PRD number
  "modelId" INTEGER REFERENCES "models" ("modelId"),
  "name" TEXT   -- e.g. Unlocked USA, Black KEYone
);

CREATE VIEW "full_device_names" AS
  SELECT * FROM "families" f
  LEFT JOIN "models" m ON m.familyId=f.familyId
  LEFT JOIN "devices" d ON d.modelId=m.modelId;

-- we only care about the first file for now
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

CREATE VIEW "updates_files" AS
  SELECT * FROM "updates" u
  LEFT JOIN "files" f ON u.file_sha1=f.sha1;
