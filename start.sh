#!/bin/bash

echo "🚀 بدء تشغيل مشروع عقار زين..."

# تشغيل Backend في الخلفية
cd backend
php artisan serve &
BACKEND_PID=$!
cd ..

# تشغيل Frontend
cd frontend
npm run dev &
FRONTEND_PID=$!
cd ..

echo "✅ Backend يعمل على: http://localhost:8000"
echo "✅ Frontend يعمل على: http://localhost:5173"
echo ""
echo "لإيقاف المشروع: press Ctrl+C"

wait
