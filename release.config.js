/* eslint-disable @typescript-eslint/no-var-requires */
module.exports = {
  branches: ["master"],
  plugins: [
    ["@semantic-release/changelog", { changelogFile: "CHANGELOG.md" }],
    "@semantic-release/commit-analyzer",
    "@semantic-release/release-notes-generator",
    ["@semantic-release/git", {
      assets: ["CHANGELOG.md"],
      message: "chore(release): ${nextRelease.version} [skip ci]\n\n${nextRelease.notes}",
    }],
    ["@semantic-release/wordpress", {
      type: "plugin",
      slug: "srbtranslatin",
      withVersionFile: true,
      withAssets: true,
      withReadme: true,
      releasePath: "/tmp/wp-release",
    }],
    ["./scripts/validate-package.cjs", { releasePath: "/tmp/wp-release", slug: "srbtranslatin" }],
    ["@semantic-release/github", {
      assets: [{
        path: "/tmp/wp-release/package.zip",
        label: "SrbTransLatin v${nextRelease.version}",
        name: "srbtranslatin-${nextRelease.version}.zip",
      }],
    }],
  ],
};
