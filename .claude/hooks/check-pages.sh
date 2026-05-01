#!/bin/bash

# Reads the project domain from a config file or falls back to a default
DOMAIN="${VALET_DOMAIN:-myproject.test}"
PAGES_FILE=".claude/check-pages.txt"

# Only run if a pages file exists
if [ ! -f "$PAGES_FILE" ]; then
  exit 0
fi

FAILED=0

while IFS= read -r path || [ -n "$path" ]; do
  # Skip blank lines and comments
  [[ -z "$path" || "$path" == \#* ]] && continue

  URL="https://${DOMAIN}${path}"
  STATUS=$(curl -s -o /dev/null -w "%{http_code}" --max-time 5 "$URL")

  if [ "$STATUS" = "200" ] || [ "$STATUS" = "302" ]; then
    echo "✅ $URL ($STATUS)"
  else
    echo "❌ $URL returned HTTP $STATUS" >&2
    FAILED=1
  fi
done < "$PAGES_FILE"

if [ "$FAILED" -eq 1 ]; then
  echo "One or more pages failed to load. Check the errors above." >&2
  exit 2  # Sends feedback to Claude so it knows something is wrong
fi

exit 0
