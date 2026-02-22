import React, { useState } from 'react';
import { FaPhone, FaEnvelope, FaMapMarkerAlt, FaFacebook, FaTwitter, FaInstagram, FaLinkedin, FaWhatsapp, FaTelegram } from 'react-icons/fa';
import toast from 'react-hot-toast';

const ContactPage = () => {
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    phone: '',
    subject: '',
    message: ''
  });
  const [loading, setLoading] = useState(false);

  const handleChange = (e) => {
    setFormData({
      ...formData,
      [e.target.name]: e.target.value
    });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    
    // محاكاة إرسال الرسالة
    setTimeout(() => {
      toast.success('تم إرسال رسالتك بنجاح، سنتواصل معك قريباً');
      setFormData({
        name: '',
        email: '',
        phone: '',
        subject: '',
        message: ''
      });
      setLoading(false);
    }, 1500);
  };

  return (
    <div className="min-h-screen bg-gray-50 py-8">
      <div className="container mx-auto px-4">
        {/* Header */}
        <div className="text-center mb-12">
          <h1 className="text-4xl font-bold mb-4" data-aos="fade-down">اتصل بنا</h1>
          <p className="text-gray-600 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="100">
            تواصل معنا لأي استفسار أو مساعدة. فريقنا جاهز للرد على جميع استفساراتك
          </p>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
          {/* معلومات الاتصال */}
          <div className="space-y-6">
            <div className="bg-white rounded-2xl shadow-lg p-8" data-aos="fade-left">
              <h2 className="text-2xl font-bold mb-6">معلومات الاتصال</h2>
              
              <div className="space-y-6">
                <div className="flex items-start gap-4">
                  <div className="bg-primary-100 p-3 rounded-lg">
                    <FaPhone className="text-primary-600 text-xl" />
                  </div>
                  <div>
                    <h3 className="font-semibold mb-1">الهاتف</h3>
                    <a href="tel:+201234567890" className="text-gray-600 hover:text-primary-600">
                      +20 123 456 7890
                    </a>
                  </div>
                </div>

                <div className="flex items-start gap-4">
                  <div className="bg-primary-100 p-3 rounded-lg">
                    <FaEnvelope className="text-primary-600 text-xl" />
                  </div>
                  <div>
                    <h3 className="font-semibold mb-1">البريد الإلكتروني</h3>
                    <a href="mailto:info@zain-realestate.com" className="text-gray-600 hover:text-primary-600">
                      info@zain-realestate.com
                    </a>
                  </div>
                </div>

                <div className="flex items-start gap-4">
                  <div className="bg-primary-100 p-3 rounded-lg">
                    <FaMapMarkerAlt className="text-primary-600 text-xl" />
                  </div>
                  <div>
                    <h3 className="font-semibold mb-1">العنوان</h3>
                    <p className="text-gray-600">
                      شارع قناة السويس، الإسماعيلية<br />
                      مصر
                    </p>
                  </div>
                </div>
              </div>
            </div>

            {/* وسائل التواصل الاجتماعي */}
            <div className="bg-white rounded-2xl shadow-lg p-8" data-aos="fade-left" data-aos-delay="100">
              <h2 className="text-2xl font-bold mb-6">تابعنا على</h2>
              <div className="flex flex-wrap gap-4">
                <a href="#" className="social-icon bg-blue-600 hover:bg-blue-700">
                  <FaFacebook />
                </a>
                <a href="#" className="social-icon bg-sky-500 hover:bg-sky-600">
                  <FaTwitter />
                </a>
                <a href="#" className="social-icon bg-pink-600 hover:bg-pink-700">
                  <FaInstagram />
                </a>
                <a href="#" className="social-icon bg-blue-700 hover:bg-blue-800">
                  <FaLinkedin />
                </a>
                <a href="#" className="social-icon bg-green-600 hover:bg-green-700">
                  <FaWhatsapp />
                </a>
                <a href="#" className="social-icon bg-blue-500 hover:bg-blue-600">
                  <FaTelegram />
                </a>
              </div>
            </div>

            {/* ساعات العمل */}
            <div className="bg-white rounded-2xl shadow-lg p-8" data-aos="fade-left" data-aos-delay="200">
              <h2 className="text-2xl font-bold mb-6">ساعات العمل</h2>
              <div className="space-y-3">
                <div className="flex justify-between">
                  <span className="text-gray-600">السبت - الخميس</span>
                  <span className="font-semibold">9:00 ص - 9:00 م</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-gray-600">الجمعة</span>
                  <span className="font-semibold text-red-600">مغلق</span>
                </div>
              </div>
            </div>
          </div>

          {/* نموذج الاتصال */}
          <div className="bg-white rounded-2xl shadow-lg p-8" data-aos="fade-right">
            <h2 className="text-2xl font-bold mb-6">أرسل لنا رسالة</h2>
            
            <form onSubmit={handleSubmit} className="space-y-6">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label className="block text-gray-700 mb-2">الاسم الكامل *</label>
                  <input
                    type="text"
                    name="name"
                    value={formData.name}
                    onChange={handleChange}
                    required
                    className="input w-full"
                    placeholder="أحمد محمد"
                  />
                </div>

                <div>
                  <label className="block text-gray-700 mb-2">البريد الإلكتروني *</label>
                  <input
                    type="email"
                    name="email"
                    value={formData.email}
                    onChange={handleChange}
                    required
                    className="input w-full"
                    placeholder="example@email.com"
                  />
                </div>
              </div>

              <div>
                <label className="block text-gray-700 mb-2">رقم الهاتف</label>
                <input
                  type="tel"
                  name="phone"
                  value={formData.phone}
                  onChange={handleChange}
                  className="input w-full"
                  placeholder="01234567890"
                />
              </div>

              <div>
                <label className="block text-gray-700 mb-2">الموضوع *</label>
                <input
                  type="text"
                  name="subject"
                  value={formData.subject}
                  onChange={handleChange}
                  required
                  className="input w-full"
                  placeholder="موضوع الرسالة"
                />
              </div>

              <div>
                <label className="block text-gray-700 mb-2">الرسالة *</label>
                <textarea
                  name="message"
                  value={formData.message}
                  onChange={handleChange}
                  required
                  rows="6"
                  className="input w-full"
                  placeholder="اكتب رسالتك هنا..."
                ></textarea>
              </div>

              <button
                type="submit"
                disabled={loading}
                className="btn-primary w-full py-4 text-lg"
              >
                {loading ? 'جاري الإرسال...' : 'إرسال الرسالة'}
              </button>
            </form>
          </div>
        </div>

        {/* الخريطة */}
        <div className="mt-12 bg-white rounded-2xl shadow-lg p-8" data-aos="zoom-in">
          <h2 className="text-2xl font-bold mb-6">موقعنا</h2>
          <div className="h-96 rounded-xl overflow-hidden">
            <iframe
              title="map"
              width="100%"
              height="100%"
              frameBorder="0"
              src="https://www.openstreetmap.org/export/embed.html?bbox=32.2623%2C30.5846%2C32.3023%2C30.6246&layer=mapnik&marker=30.6046%2C32.2723"
              style={{ border: 0 }}
            ></iframe>
          </div>
        </div>
      </div>
    </div>
  );
};

export default ContactPage;
