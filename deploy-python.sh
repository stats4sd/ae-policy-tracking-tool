#!/usr/bin/env bash
set -e

if [ ! -f "venv/bin/python3" ]; then
    echo "Creating Python virtual environment..."
    python3 -m venv venv
fi

echo "Activating virtual environment..."
source venv/bin/activate

echo "Installing Python dependencies..."
pip install -r requirements.txt

echo "Python environment ready."
