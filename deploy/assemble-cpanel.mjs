#!/usr/bin/env node
/**
 * Assemble deploy/cpanel-upload from frontend build + backend (cross-platform).
 * Used by GitHub Actions; Windows can keep using build-cpanel.bat.
 */
import { cpSync, existsSync, mkdirSync, rmSync, copyFileSync } from "fs";
import { join, dirname } from "path";
import { fileURLToPath } from "url";
import { execSync } from "child_process";

const __dirname = dirname(fileURLToPath(import.meta.url));
const ROOT = join(__dirname, "..");
const PKG = join(__dirname, "cpanel-upload");
const FRONTEND = join(ROOT, "frontend");
const BACKEND = join(ROOT, "backend");

console.log("=== Assemble cPanel package ===");

if (existsSync(PKG)) rmSync(PKG, { recursive: true, force: true });
mkdirSync(PKG, { recursive: true });
mkdirSync(join(PKG, "uploads"), { recursive: true });

console.log("[1/4] Copy frontend export...");
cpSync(join(FRONTEND, "out"), PKG, { recursive: true });

console.log("[2/4] Rename _next → next...");
execSync(`node "${join(__dirname, "rename-next-assets.mjs")}" "${PKG}"`, { stdio: "inherit" });
if (existsSync(join(PKG, "_next"))) rmSync(join(PKG, "_next"), { recursive: true, force: true });
if (existsSync(join(PKG, "next"))) {
  copyFileSync(join(__dirname, "next-htaccess"), join(PKG, "next", ".htaccess"));
}

console.log("[3/4] Copy backend...");
cpSync(join(BACKEND, "api"), join(PKG, "api"), { recursive: true });
cpSync(join(BACKEND, "admin"), join(PKG, "admin"), { recursive: true });
cpSync(join(BACKEND, "includes"), join(PKG, "includes"), { recursive: true });
copyFileSync(join(BACKEND, ".htaccess"), join(PKG, ".htaccess"));
for (const f of ["setup.php", "config.example.php", "schema.sql"]) {
  const src = f === "schema.sql" ? join(BACKEND, "db", "schema.sql") : join(BACKEND, f);
  if (existsSync(src)) copyFileSync(src, join(PKG, f === "schema.sql" ? "schema.sql" : f));
}

console.log("[4/4] Done →", PKG);
