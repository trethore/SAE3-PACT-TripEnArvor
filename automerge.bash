@echo off
setlocal enabledelayedexpansion

:: Fetch the latest changes
echo Fetching latest changes...
git fetch origin

:: Get the current branch name
for /f "tokens=*" %%i in ('git rev-parse --abbrev-ref HEAD') do set CURRENT_BRANCH=%%i

:: Switch to the main branch
echo Switching to main branch...
git checkout main

:: Ensure the local main branch is up to date
echo Pulling latest changes from origin/main...
git pull origin main

:: Merge the current branch into main
echo Merging %CURRENT_BRANCH% into main...
git merge %CURRENT_BRANCH% --no-ff

:: Push the changes to the remote main branch
echo Pushing the changes to origin/main...
git push origin main

:: Switch back to the original branch
echo Switching back to %CURRENT_BRANCH%...
git checkout %CURRENT_BRANCH%

echo Merge completed successfully!
pause