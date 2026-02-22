import React from 'react';
import { Link } from 'react-router-dom';
import { FaFacebook, FaTwitter, FaInstagram, FaLinkedin, FaPhone, FaEnvelope, FaMapMarkerAlt } from 'react-icons/fa';

const Footer = () => {
  return (
    <footer className="bg-gray-900 text-white pt-12 pb-6">
      <div className="container mx-auto px-4">
        <div className="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
          {/* About */}
          <div>
            <h3 className="text-xl font-bold mb-4">عقار زين</h3>
            <p className="text-gray-400 mb-4">
              منصة عقارية رائدة في الإسماعيلية، نساعدك في العثور على منزل أحلامك بكل سهولة ويسر.
            </p>
            <div className="flex gap-4">
              <a href="#" className="text-gray-400 hover:text-white transition"><FaFacebook size={20} /></a>
              <a href="#" className="text-gray-400 hover:text-white transition"><FaTwitter size={20} /></a>
              <a href="#" className="text-gray-400 hover:text-white transition"><FaInstagram size={20} /></a>
              <a href="#" className="text-gray-400 hover:text-white transition"><FaLinkedin size={20} /></a>
            </div>
          </div>

          {/* Quick Links */}
          <div>
            <h3 className="text-xl font-bold mb-4">روابط سريعة</h3>
            <ul className="space-y-2">
              <li><Link to="/" className="text-gray-400 hover:text-white transition">الرئيسية</Link></li>
              <li><Link to="/properties" className="text-gray-400 hover:text-white transition">عقارات</Link></li>
              <li><Link to="/categories" className="text-gray-400 hover:text-white transition">تصنيفات</Link></li>
              <li><Link to="/locations" className="text-gray-400 hover:text-white transition">مواقع</Link></li>
              <li><Link to="/about" className="text-gray-400 hover:text-white transition">من نحن</Link></li>
              <li><Link to="/contact" className="text-gray-400 hover:text-white transition">اتصل بنا</Link></li>
            </ul>
          </div>

          {/* Categories */}
          <div>
            <h3 className="text-xl font-bold mb-4">تصنيفات عقارية</h3>
            <ul className="space-y-2">
              <li><Link to="/categories/apartment" className="text-gray-400 hover:text-white transition">شقق</Link></li>
              <li><Link to="/categories/villa" className="text-gray-400 hover:text-white transition">فلل</Link></li>
              <li><Link to="/categories/house" className="text-gray-400 hover:text-white transition">منازل</Link></li>
              <li><Link to="/categories/shop" className="text-gray-400 hover:text-white transition">محلات تجارية</Link></li>
              <li><Link to="/categories/office" className="text-gray-400 hover:text-white transition">مكاتب</Link></li>
              <li><Link to="/categories/land" className="text-gray-400 hover:text-white transition">أراضي</Link></li>
            </ul>
          </div>

          {/* Contact */}
          <div>
            <h3 className="text-xl font-bold mb-4">اتصل بنا</h3>
            <ul className="space-y-3">
              <li className="flex items-center gap-3 text-gray-400">
                <FaMapMarkerAlt className="text-primary-500" />
                <span>الإسماعيلية - مصر</span>
              </li>
              <li className="flex items-center gap-3 text-gray-400">
                <FaPhone className="text-primary-500" />
                <span dir="ltr">+20 123 456 7890</span>
              </li>
              <li className="flex items-center gap-3 text-gray-400">
                <FaEnvelope className="text-primary-500" />
                <span>info@zain-realestate.com</span>
              </li>
            </ul>
          </div>
        </div>

        {/* Copyright */}
        <div className="border-t border-gray-800 pt-6 text-center text-gray-400">
          <p>© {new Date().getFullYear()} عقار زين. جميع الحقوق محفوظة</p>
        </div>
      </div>
    </footer>
  );
};

export default Footer;
