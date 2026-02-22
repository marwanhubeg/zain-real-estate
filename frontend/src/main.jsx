import React from 'react';
import ReactDOM from 'react-dom/client';
import App from './App';
import { AuthProvider } from './context/AuthContext';
import './index.css';

// استيراد مكتبة AOS للتمرير
import AOS from 'aos';
import 'aos/dist/aos.css';

// تهيئة AOS مع إعدادات متقدمة
AOS.init({
  // مدة الحركة بالمللي ثانية
  duration: 1000,
  
  // تشغيل الحركة مرة واحدة فقط
  once: true,
  
  // مسافة التمرير قبل تشغيل الحركة (بالبكسل)
  offset: 100,
  
  // نوع الحركة
  easing: 'ease-out-cubic',
  
  // تأخير الحركة
  delay: 0,
  
  // إذا كان العنصر يظهر مرة واحدة فقط
  mirror: false,
  
  // إذا كان العنصر يختفي عند التمرير لأعلى
  anchorPlacement: 'top-bottom',
  
  // تمكين الحركات على الأجهزة المحمولة
  disable: false,
  
  // بدء الحركة فوراً
  startEvent: 'DOMContentLoaded',
  
  // إعدادات إضافية
  useClassNames: false,
  disableMutationObserver: false,
  
  // أنواع الحركات المسبقة
  // يمكنك استخدام: fade-up, fade-down, fade-left, fade-right, flip-up, flip-down, flip-left, flip-right, zoom-in, zoom-out
});

// إضافة حدث لإعادة تهيئة AOS عند تغيير المحتوى ديناميكياً
window.addEventListener('load', () => {
  AOS.refresh();
});

ReactDOM.createRoot(document.getElementById('root')).render(
  <React.StrictMode>
    <AuthProvider>
      <App />
    </AuthProvider>
  </React.StrictMode>
);
