# PowerShell script to release new version
# Usage: .\release.ps1

$VERSION = "v1.1.0"
$MESSAGE = "Release $VERSION - Simplified to single site configuration"

Write-Host "🚀 Starting release process for $VERSION..." -ForegroundColor Cyan

# Check if we're on main/master branch
$CURRENT_BRANCH = git branch --show-current
if ($CURRENT_BRANCH -ne "main" -and $CURRENT_BRANCH -ne "master") {
    Write-Host "⚠️  Warning: You're not on main/master branch. Current branch: $CURRENT_BRANCH" -ForegroundColor Yellow
    $response = Read-Host "Continue anyway? (y/n)"
    if ($response -ne "y" -and $response -ne "Y") {
        exit 1
    }
}

# Check for uncommitted changes
$status = git status --porcelain
if ($status) {
    Write-Host "📝 You have uncommitted changes. Adding them..." -ForegroundColor Yellow
} else {
    Write-Host "✅ No uncommitted changes" -ForegroundColor Green
}

# Add all changes
Write-Host "📝 Adding all changes..." -ForegroundColor Cyan
git add .

# Commit changes
Write-Host "💾 Committing changes..." -ForegroundColor Cyan
$commitResult = git commit -m $MESSAGE 2>&1
if ($LASTEXITCODE -ne 0) {
    if ($commitResult -match "nothing to commit") {
        Write-Host "⚠️  No changes to commit" -ForegroundColor Yellow
    } else {
        Write-Host "❌ Commit failed: $commitResult" -ForegroundColor Red
        exit 1
    }
}

# Create tag
Write-Host "🏷️  Creating tag $VERSION..." -ForegroundColor Cyan
git tag -a $VERSION -m $MESSAGE
if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Failed to create tag" -ForegroundColor Red
    exit 1
}

# Push commits
Write-Host "📤 Pushing commits..." -ForegroundColor Cyan
git push origin $CURRENT_BRANCH
if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Failed to push commits" -ForegroundColor Red
    exit 1
}

# Push tags
Write-Host "📤 Pushing tags..." -ForegroundColor Cyan
git push origin $VERSION
if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Failed to push tag" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "✅ Release $VERSION completed successfully!" -ForegroundColor Green
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Cyan
Write-Host "1. Verify the release on GitHub"
Write-Host "2. Update composer.json version if needed"
Write-Host "3. Create release notes on GitHub"

