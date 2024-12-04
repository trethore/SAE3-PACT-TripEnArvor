#!/bin/bash

echo "Fetching latest changes..."
git fetch origin
current_branch=$(git rev-parse --abbrev-ref HEAD)
echo "Switching to main branch..."
git checkout main
echo "Pulling latest changes from origin/main..."
git pull origin main
echo "Merging $current_branch into main..."
git merge "$current_branch" --no-ff
echo "Pushing the changes to origin/main..."
git push origin main
echo "Switching back to $current_branch..."
git checkout "$current_branch"

echo "Merge completed successfully!"