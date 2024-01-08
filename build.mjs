import esbuild from "esbuild";

const entryPoints = ["Resources/Private/Script/*.js"];

const options = {
    logLevel: "info",
    bundle: true,
    minify: process.argv.includes("--production"),
    sourcemap: !process.argv.includes("--production"),
    target: "es2020",
    entryPoints,
    legalComments: "none",
    outdir: "Resources/Public/JavaScript",
    format: "iife",
};

async function watch(options) {
    const context = await esbuild.context(options);
    await context.watch();
}

process.argv.includes("--watch") ? watch(options) : esbuild.build(options);
