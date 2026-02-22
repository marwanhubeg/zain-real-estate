import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import { FaUser, FaEnvelope, FaPhone, FaMapMarkerAlt, FaSave, FaCamera } from 'react-icons/fa';
import toast from 'react-hot-toast';

const ProfilePage = () => {
  // بيانات تجريبية
  const [formData, setFormData] = useState({
    name: 'أحمد محمد',
    email: 'ahmed@example.com',
    phone: '01234567890',
    whatsapp: '01234567890',
    address: 'شارع قناة السويس، الإسماعيلية',
    city: 'الإسماعيلية',
    bio_ar: 'مسوق عقاري بخبرة 5 سنوات في سوق الإسماعيلية'
  });
  
  const [avatarPreview, setAvatarPreview] = useState('https://via.placeholder.com/200');
  const [loading, setLoading] = useState(false);

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    setLoading(true);
    setTimeout(() => {
      toast.success('تم تحديث الملف الشخصي بنجاح');
      setLoading(false);
    }, 1000);
  };

  return (
    <div className="min-h-screen bg-gray-50 py-8">
      <div className="container mx-auto px-4">
        <div className="max-w-4xl mx-auto">
          <h1 className="text-3xl font-bold mb-8">الملف الشخصي</h1>

          <form onSubmit={handleSubmit} className="space-y-8">
            {/* الصورة الشخصية */}
            <div className="bg-white rounded-2xl shadow-lg p-8">
              <h2 className="text-2xl font-bold mb-6">الصورة الشخصية</h2>
              <div className="flex flex-col items-center">
                <div className="relative w-48 h-48 mb-4">
                  <img src={avatarPreview} alt="Avatar" className="w-full h-full rounded-full object-cover border-4 border-primary-200" />
                  <label className="absolute bottom-0 right-0 bg-primary-600 text-white p-3 rounded-full cursor-pointer hover:bg-primary-700 transition-colors">
                    <FaCamera />
                    <input type="file" accept="image/*" className="hidden" />
                  </label>
                </div>
                <p className="text-gray-500 text-sm">اختر صورة جديدة لتحديث الصورة الشخصية</p>
              </div>
            </div>

            {/* المعلومات الأساسية */}
            <div className="bg-white rounded-2xl shadow-lg p-8">
              <h2 className="text-2xl font-bold mb-6">المعلومات الأساسية</h2>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label className="block text-gray-700 mb-2">الاسم الكامل</label>
                  <div className="relative">
                    <FaUser className="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
                    <input type="text" name="name" value={formData.name} onChange={handleChange} className="input pr-10" />
                  </div>
                </div>
                <div>
                  <label className="block text-gray-700 mb-2">البريد الإلكتروني</label>
                  <div className="relative">
                    <FaEnvelope className="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
                    <input type="email" name="email" value={formData.email} onChange={handleChange} className="input pr-10" />
                  </div>
                </div>
                <div>
                  <label className="block text-gray-700 mb-2">رقم الهاتف</label>
                  <div className="relative">
                    <FaPhone className="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
                    <input type="tel" name="phone" value={formData.phone} onChange={handleChange} className="input pr-10" />
                  </div>
                </div>
                <div>
                  <label className="block text-gray-700 mb-2">العنوان</label>
                  <div className="relative">
                    <FaMapMarkerAlt className="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
                    <input type="text" name="address" value={formData.address} onChange={handleChange} className="input pr-10" />
                  </div>
                </div>
              </div>
            </div>

            {/* السيرة الذاتية */}
            <div className="bg-white rounded-2xl shadow-lg p-8">
              <h2 className="text-2xl font-bold mb-6">نبذة عني</h2>
              <textarea name="bio_ar" value={formData.bio_ar} onChange={handleChange} rows="4" className="input w-full"></textarea>
            </div>

            {/* زر الحفظ */}
            <div className="flex justify-end">
              <button type="submit" disabled={loading} className="btn-primary flex items-center gap-2">
                <FaSave /> {loading ? 'جاري الحفظ...' : 'حفظ التغييرات'}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  );
};

export default ProfilePage;
