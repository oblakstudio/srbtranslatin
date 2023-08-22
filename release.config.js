/* eslint-disable @typescript-eslint/no-var-requires */
const { generateConfig } = require("semantic-release-wordpress-config");

module.exports = generateConfig({
  branches: ["master"],
  type: "plugin",
  changelog: "CHANGELOG.md",
  name: "SrbTransLatin",
  slug: "srbtranslatin",
  wp: {
    withVersionFile: true,
    withAssets: true,
    withReadme: true,
  },
});
