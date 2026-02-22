import React, { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import { getProperty } from '../services/api';
import { FaBed, FaBath, FaRulerCombined, FaMapMarkerAlt, FaCalendar, FaUser, FaStar, FaPhone, FaWhatsapp, FaHeart, FaRegHeart } from 'react-icons/fa';
import toast from 'react-hot-toast';
import PropertyCard from '../components/properties/PropertyCard';

const PropertyDetailsPage = () => {
  const { slug } = useParams();
  const [property, setProperty] = useState(null);
  const [loading, setLoading] = useState(true);
  const [selectedImage, setSelectedImage] = useState(0);
  const [isFavorite, setIsFavorite] = useState(false);
  const [bookingModal, setBookingModal] = useState(false);
  const [bookingData, setBookingData] = useState({
    booking_date: '',
    booking_time: '',
    notes: ''
  });

  useEffect(() => {
    fetchProperty();
    // التحقق من المفضلة
    const favorites = JSON.parse(localStorage.getItem('favorites') || '[]');
    setIsFavorite(favorites.includes(slug));
  }, [slug]);

  const fetchProperty = async () => {
    setLoading(true);
    try {
      const res = await getProperty(slug);
      setProperty(res.data.data);
    } catch (error) {
      toast.error('حدث خطأ في جلب بيانات العقار');
      console.error('Error fetching property:', error);
    } finally {
      setLoading(false);
    }
  };

  const toggleFavorite = () => {
    const favorites = JSON.parse(localStorage.getItem('favorites') || '[]');
    let newFavorites;
    
    if (isFavorite) {
      newFavorites = favorites.filter(s => s !== slug);
      toast.success('تم إزالة العقار من المفضلة');
    } else {
      newFavorites = [...favorites, slug];
      toast.success('تم إضافة العقار إلى المفضلة');
    }
    
    localStorage.setItem('favorites', JSON.stringify(newFavorites));
    setIsFavorite(!isFavorite);
  };

  const handleBooking = (e) => {
    e.preventDefault();
    toast.success('تم إرسال طلب الحجز بنجاح');
    setBookingModal(false);
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-gray-50 py-8">
        <div className="container mx-auto px-4">
          <div className="animate-pulse">
            <div className="h-96 bg-gray-300 rounded-lg mb-6"></div>
            <div className="grid grid-cols-3 gap-6">
              <div className="col-span-2">
                <div className="h-8 bg-gray-300 rounded w-3/4 mb-4"></div>
                <div className="h-4 bg-gray-300 rounded w-1/2 mb-2"></div>
                <div className="h-4 bg-gray-300 rounded w-2/3"></div>
              </div>
              <div className="h-64 bg-gray-300 rounded"></div>
            </div>
          </div>
        </div>
      </div>
    );
  }

  if (!property) {
    return (
      <div className="min-h-screen bg-gray-50 py-8">
        <div className="container mx-auto px-4 text-center">
          <h1 className="text-2xl font-bold mb-4">العقار غير موجود</h1>
          <Link to="/properties" className="btn-primary">العودة للعقارات</Link>
        </div>
      </div>
    );
  }

  const images = property.gallery || [property.main_image];
  const {
    title,
    description,
    price,
    formatted_price,
    area,
    bedrooms,
    bathrooms,
    location,
    address,
    type_text,
    status_text,
    finishing_type_text,
    payment_method,
    installment_years,
    down_payment,
    monthly_payment,
    category,
    user,
    amenities = [],
    created_at,
    views_count,
    reviews_count,
    reviews_avg_rating
  } = property;

  return (
    <div className="min-h-screen bg-gray-50 py-8">
      <div className="container mx-auto px-4">
        {/* Breadcrumb */}
        <div className="mb-6">
          <nav className="flex text-gray-500 text-sm">
            <Link to="/" className="hover:text-primary-600">الرئيسية</Link>
            <span className="mx-2">/</span>
            <Link to="/properties" className="hover:text-primary-600">عقارات</Link>
            <span className="mx-2">/</span>
            <span className="text-gray-900">{title}</span>
          </nav>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          {/* Main Content */}
          <div className="lg:col-span-2">
            {/* Image Gallery */}
            <div className="bg-white rounded-lg shadow-md overflow-hidden mb-6">
              <div className="h-96">
                <img 
                  src={images[selectedImage]} 
                  alt={title}
                  className="w-full h-full object-cover"
                />
              </div>
              {images.length > 1 && (
                <div className="flex gap-2 p-4 overflow-x-auto">
                  {images.map((img, index) => (
                    <button
                      key={index}
                      onClick={() => setSelectedImage(index)}
                      className={`flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden border-2 ${
                        selectedImage === index ? 'border-primary-600' : 'border-transparent'
                      }`}
                    >
                      <img src={img} alt="" className="w-full h-full object-cover" />
                    </button>
                  ))}
                </div>
              )}
            </div>

            {/* Property Info */}
            <div className="bg-white rounded-lg shadow-md p-6 mb-6">
              <h1 className="text-3xl font-bold mb-4">{title}</h1>
              
              <div className="flex items-center gap-4 text-gray-600 mb-4">
                <span className="flex items-center gap-1">
                  <FaMapMarkerAlt className="text-primary-600" />
                  {address || location}
                </span>
                <span className="flex items-center gap-1">
                  <FaCalendar className="text-primary-600" />
                  {new Date(created_at).toLocaleDateString('ar-EG')}
                </span>
                <span className="flex items-center gap-1">
                  <FaStar className="text-yellow-500" />
                  {reviews_avg_rating || 0} ({reviews_count || 0} تقييم)
                </span>
              </div>

              <div className="flex flex-wrap gap-2 mb-6">
                <span className="bg-primary-100 text-primary-800 px-3 py-1 rounded-full text-sm">
                  {type_text}
                </span>
                <span className="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">
                  {status_text}
                </span>
                <span className="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                  {finishing_type_text}
                </span>
              </div>

              <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div className="text-center p-3 bg-gray-50 rounded-lg">
                  <FaBed className="text-2xl text-primary-600 mx-auto mb-1" />
                  <div className="font-bold">{bedrooms}</div>
                  <div className="text-sm text-gray-500">غرف نوم</div>
                </div>
                <div className="text-center p-3 bg-gray-50 rounded-lg">
                  <FaBath className="text-2xl text-primary-600 mx-auto mb-1" />
                  <div className="font-bold">{bathrooms}</div>
                  <div className="text-sm text-gray-500">حمامات</div>
                </div>
                <div className="text-center p-3 bg-gray-50 rounded-lg">
                  <FaRulerCombined className="text-2xl text-primary-600 mx-auto mb-1" />
                  <div className="font-bold">{area} م²</div>
                  <div className="text-sm text-gray-500">المساحة</div>
                </div>
                <div className="text-center p-3 bg-gray-50 rounded-lg">
                  <FaStar className="text-2xl text-primary-600 mx-auto mb-1" />
                  <div className="font-bold">{views_count}</div>
                  <div className="text-sm text-gray-500">مشاهدة</div>
                </div>
              </div>

              <div className="mb-6">
                <h2 className="text-xl font-bold mb-3">الوصف</h2>
                <p className="text-gray-700 leading-relaxed whitespace-pre-line">
                  {description}
                </p>
              </div>

              {amenities.length > 0 && (
                <div className="mb-6">
                  <h2 className="text-xl font-bold mb-3">المزايا</h2>
                  <div className="grid grid-cols-2 md:grid-cols-3 gap-3">
                    {amenities.map(amenity => (
                      <div key={amenity.id} className="flex items-center gap-2">
                        <span className="text-primary-600">{amenity.icon || '✓'}</span>
                        <span>{amenity.name}</span>
                      </div>
                    ))}
                  </div>
                </div>
              )}

              {/* Payment Info */}
              {payment_method && (
                <div className="mb-6">
                  <h2 className="text-xl font-bold mb-3">معلومات الدفع</h2>
                  <div className="bg-gray-50 p-4 rounded-lg">
                    <p><strong>طريقة الدفع:</strong> {payment_method === 'cash' ? 'كاش' : payment_method === 'installment' ? 'تقسيط' : 'كل الطرق'}</p>
                    {installment_years && (
                      <>
                        <p><strong>سنوات التقسيط:</strong> {installment_years} سنوات</p>
                        <p><strong>دفعة مقدمة:</strong> {down_payment?.toLocaleString()} جنيه</p>
                        <p><strong>قسط شهري:</strong> {monthly_payment?.toLocaleString()} جنيه</p>
                      </>
                    )}
                  </div>
                </div>
              )}
            </div>
          </div>

          {/* Sidebar */}
          <div className="lg:col-span-1">
            {/* Price Card */}
            <div className="bg-white rounded-lg shadow-md p-6 mb-6 sticky top-20">
              <div className="text-3xl font-bold text-primary-600 mb-4">
                {formatted_price}
              </div>
              
              <div className="space-y-3 mb-6">
                <Link
                  to={`https://wa.me/201234567890?text=${encodeURIComponent(`أريد استفسار عن العقار: ${title}`)}`}
                  target="_blank"
                  className="btn-outline w-full flex items-center justify-center gap-2 text-green-600 border-green-600 hover:bg-green-50"
                >
                  <FaWhatsapp /> واتساب
                </Link>
                <button
                  onClick={() => window.location.href = `tel:+201234567890`}
                  className="btn-outline w-full flex items-center justify-center gap-2"
                >
                  <FaPhone /> اتصال
                </button>
                <button
                  onClick={toggleFavorite}
                  className={`w-full flex items-center justify-center gap-2 py-2 rounded-lg border ${
                    isFavorite 
                      ? 'bg-red-50 text-red-600 border-red-300' 
                      : 'hover:bg-gray-50'
                  }`}
                >
                  {isFavorite ? <FaHeart /> : <FaRegHeart />}
                  {isFavorite ? 'إزالة من المفضلة' : 'إضافة إلى المفضلة'}
                </button>
              </div>

              <button
                onClick={() => setBookingModal(true)}
                className="btn-primary w-full mb-4"
              >
                حجز موعد معاينة
              </button>

              {/* Agent Info */}
              {user && (
                <div className="border-t pt-4">
                  <h3 className="font-bold mb-3">المسوق العقاري</h3>
                  <div className="flex items-center gap-3">
                    <img 
                      src={user.avatar || 'https://via.placeholder.com/50'} 
                      alt={user.name}
                      className="w-12 h-12 rounded-full object-cover"
                    />
                    <div>
                      <div className="font-semibold">{user.name}</div>
                      <div className="text-sm text-gray-500">{user.role_text}</div>
                    </div>
                  </div>
                </div>
              )}
            </div>
          </div>
        </div>

        {/* Similar Properties */}
        {property.similar && property.similar.length > 0 && (
          <div className="mt-12">
            <h2 className="text-2xl font-bold mb-6">عقارات مشابهة</h2>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
              {property.similar.map(prop => (
                <PropertyCard key={prop.id} property={prop} />
              ))}
            </div>
          </div>
        )}
      </div>

      {/* Booking Modal */}
      {bookingModal && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
          <div className="bg-white rounded-lg max-w-md w-full p-6">
            <h2 className="text-2xl font-bold mb-4">حجز موعد معاينة</h2>
            
            <form onSubmit={handleBooking}>
              <div className="mb-4">
                <label className="block text-gray-700 mb-2">تاريخ المعاينة</label>
                <input
                  type="date"
                  required
                  min={new Date().toISOString().split('T')[0]}
                  className="input w-full"
                  value={bookingData.booking_date}
                  onChange={(e) => setBookingData({...bookingData, booking_date: e.target.value})}
                />
              </div>
              
              <div className="mb-4">
                <label className="block text-gray-700 mb-2">وقت المعاينة</label>
                <select
                  required
                  className="input w-full"
                  value={bookingData.booking_time}
                  onChange={(e) => setBookingData({...bookingData, booking_time: e.target.value})}
                >
                  <option value="">اختر الوقت</option>
                  <option value="09:00">9:00 صباحاً</option>
                  <option value="10:00">10:00 صباحاً</option>
                  <option value="11:00">11:00 صباحاً</option>
                  <option value="12:00">12:00 ظهراً</option>
                  <option value="13:00">1:00 مساءً</option>
                  <option value="14:00">2:00 مساءً</option>
                  <option value="15:00">3:00 مساءً</option>
                  <option value="16:00">4:00 مساءً</option>
                  <option value="17:00">5:00 مساءً</option>
                </select>
              </div>
              
              <div className="mb-6">
                <label className="block text-gray-700 mb-2">ملاحظات (اختياري)</label>
                <textarea
                  className="input w-full h-24"
                  value={bookingData.notes}
                  onChange={(e) => setBookingData({...bookingData, notes: e.target.value})}
                  placeholder="أي ملاحظات إضافية..."
                ></textarea>
              </div>
              
              <div className="flex gap-3">
                <button type="submit" className="btn-primary flex-1">
                  تأكيد الحجز
                </button>
                <button
                  type="button"
                  onClick={() => setBookingModal(false)}
                  className="btn-outline px-6"
                >
                  إلغاء
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};

export default PropertyDetailsPage;
