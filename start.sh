#!/bin/bash
echo "🚀 Starting Zain Real Estate Platform..."
cd backend
php artisan serve --host=0.0.0.0 --port=8000 &
cd ../frontend
npm run dev -- --host 0.0.0.0 --port=3000 &
echo "✅ Platform running!"
wait
