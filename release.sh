#!/bin/bash

# Script to release new version
# Usage: ./release.sh

VERSION="v1.1.0"
MESSAGE="Release $VERSION - Simplified to single site configuration"

echo "🚀 Starting release process for $VERSION..."

# Check if we're on main/master branch
CURRENT_BRANCH=$(git branch --show-current)
if [ "$CURRENT_BRANCH" != "main" ] && [ "$CURRENT_BRANCH" != "master" ]; then
    echo "⚠️  Warning: You're not on main/master branch. Current branch: $CURRENT_BRANCH"
    read -p "Continue anyway? (y/n) " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        exit 1
    fi
fi

# Check for uncommitted changes
if ! git diff-index --quiet HEAD --; then
    echo "❌ You have uncommitted changes. Please commit or stash them first."
    exit 1
fi

# Add all changes
echo "📝 Adding all changes..."
git add .

# Commit changes
echo "💾 Committing changes..."
git commit -m "$MESSAGE" || {
    echo "⚠️  No changes to commit or commit failed"
}

# Create tag
echo "🏷️  Creating tag $VERSION..."
git tag -a "$VERSION" -m "$MESSAGE"

# Push commits
echo "📤 Pushing commits..."
git push origin "$CURRENT_BRANCH"

# Push tags
echo "📤 Pushing tags..."
git push origin "$VERSION"

echo "✅ Release $VERSION completed successfully!"
echo ""
echo "Next steps:"
echo "1. Verify the release on GitHub"
echo "2. Update composer.json version if needed"
echo "3. Create release notes on GitHub"

