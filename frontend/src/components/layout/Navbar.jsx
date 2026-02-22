import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import { FaUser, FaBars, FaTimes, FaHeart, FaCalendar } from 'react-icons/fa';

const Navbar = () => {
  const [isOpen, setIsOpen] = useState(false);
  // إزالة useAuth تماماً - نستخدم بيانات تجريبية
  const user = null; // أو { name: 'زائر' } إذا أردت

  return (
    <nav className="bg-white shadow-lg sticky top-0 z-50">
      <div className="container mx-auto px-4">
        <div className="flex justify-between items-center h-16">
          {/* Logo */}
          <Link to="/" className="flex items-center space-x-2">
            <span className="text-2xl font-bold text-primary-600">عقار زين</span>
          </Link>

          {/* Desktop Menu */}
          <div className="hidden md:flex items-center space-x-8 space-x-reverse">
            <Link to="/" className="hover:text-primary-600 transition">الرئيسية</Link>
            <Link to="/properties" className="hover:text-primary-600 transition">عقارات</Link>
            <Link to="/categories" className="hover:text-primary-600 transition">تصنيفات</Link>
            <Link to="/locations" className="hover:text-primary-600 transition">مواقع</Link>
            <Link to="/about" className="hover:text-primary-600 transition">من نحن</Link>
            <Link to="/contact" className="hover:text-primary-600 transition">اتصل بنا</Link>
            <Link to="/predictions" className="hover:text-primary-600 transition">تنبؤات</Link>
          </div>

          {/* User Menu - بدون مصادقة */}
          <div className="hidden md:flex items-center gap-2">
            <Link to="/favorites" className="p-2 hover:bg-gray-100 rounded-lg" title="المفضلة">
              <FaHeart />
            </Link>
            <Link to="/bookings" className="p-2 hover:bg-gray-100 rounded-lg" title="الحجوزات">
              <FaCalendar />
            </Link>
            <Link to="/profile" className="btn-outline">الملف الشخصي</Link>
          </div>

          {/* Mobile Menu Button */}
          <button className="md:hidden text-2xl" onClick={() => setIsOpen(!isOpen)}>
            {isOpen ? <FaTimes /> : <FaBars />}
          </button>
        </div>

        {/* Mobile Menu */}
        {isOpen && (
          <div className="md:hidden py-4 border-t">
            <Link to="/" className="block py-2 hover:text-primary-600">الرئيسية</Link>
            <Link to="/properties" className="block py-2 hover:text-primary-600">عقارات</Link>
            <Link to="/categories" className="block py-2 hover:text-primary-600">تصنيفات</Link>
            <Link to="/locations" className="block py-2 hover:text-primary-600">مواقع</Link>
            <Link to="/favorites" className="block py-2 hover:text-primary-600">المفضلة</Link>
            <Link to="/bookings" className="block py-2 hover:text-primary-600">الحجوزات</Link>
            <Link to="/profile" className="block py-2 hover:text-primary-600">الملف الشخصي</Link>
          </div>
        )}
      </div>
    </nav>
  );
};

export default Navbar;
