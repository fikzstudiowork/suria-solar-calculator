/**
 * cPanel/LiteSpeed often blocks the _next folder. Rename to /next/ and update references.
 */
import fs from "fs";
import path from "path";

const outDir = process.argv[2];
if (!outDir) {
  console.error("Usage: node rename-next-assets.mjs <out-directory>");
  process.exit(1);
}

const oldDir = path.join(outDir, "_next");
const newDir = path.join(outDir, "next");

if (!fs.existsSync(oldDir)) {
  console.log("No _next folder — skip");
  process.exit(0);
}

if (fs.existsSync(newDir)) fs.rmSync(newDir, { recursive: true, force: true });
fs.renameSync(oldDir, newDir);

const exts = new Set([".html", ".js", ".css", ".txt", ".json"]);
let count = 0;

function walk(dir) {
  for (const name of fs.readdirSync(dir)) {
    const full = path.join(dir, name);
    const stat = fs.statSync(full);
    if (stat.isDirectory()) {
      walk(full);
      continue;
    }
    const ext = path.extname(name);
    if (!exts.has(ext)) continue;
    const text = fs.readFileSync(full, "utf8");
    if (!text.includes("/_next/")) continue;
    fs.writeFileSync(full, text.replaceAll("/_next/", "/next/"));
    count++;
  }
}

walk(outDir);
console.log(`Renamed _next → next, updated ${count} files`);
