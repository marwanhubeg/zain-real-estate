import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { getCategories, getFeaturedProperties } from '../services/api';
import { getTrendingProperties, getPricePredictions } from '../services/ai';
import PropertyCard from '../components/properties/PropertyCard';
import CategoryCard from '../components/categories/CategoryCard';
import { 
  FaSearch, 
  FaMapMarkerAlt, 
  FaHome, 
  FaUsers, 
  FaHandshake,
  FaArrowLeft,
  FaChartLine,
  FaBrain,
  FaFire
} from 'react-icons/fa';

const HomePage = () => {
  const [categories, setCategories] = useState([]);
  const [featured, setFeatured] = useState([]);
  const [trending, setTrending] = useState([]);
  const [predictions, setPredictions] = useState(null);
  const [loading, setLoading] = useState(true);
  const [searchParams, setSearchParams] = useState({
    type: '',
    location: '',
    bedrooms: ''
  });

  useEffect(() => {
    fetchAllData();
  }, []);

  const fetchAllData = async () => {
    setLoading(true);
    try {
      console.log('📡 جلب البيانات...');
      
      const [catRes, featRes, trendRes, predRes] = await Promise.allSettled([
        getCategories(),
        getFeaturedProperties(),
        getTrendingProperties(4),
        getPricePredictions(null, 6)
      ]);

      // معالجة التصنيفات
      if (catRes.status === 'fulfilled') {
        console.log('✅ التصنيفات:', catRes.value.data);
        setCategories(catRes.value.data?.data || []);
      } else {
        console.error('❌ خطأ في التصنيفات:', catRes.reason);
      }

      // معالجة العقارات المميزة
      if (featRes.status === 'fulfilled') {
        console.log('✅ العقارات المميزة:', featRes.value.data);
        setFeatured(featRes.value.data?.data || []);
      } else {
        console.error('❌ خطأ في العقارات المميزة:', featRes.reason);
      }

      // معالجة الأكثر طلباً
      if (trendRes.status === 'fulfilled') {
        console.log('✅ الأكثر طلباً:', trendRes.value.data);
        setTrending(trendRes.value.data?.data || []);
      }

      // معالجة التنبؤات
      if (predRes.status === 'fulfilled' && predRes.value.data?.data) {
        setPredictions(predRes.value.data.data);
      }

    } catch (error) {
      console.error('❌ خطأ عام:', error);
    } finally {
      setLoading(false);
    }
  };

  const handleSearch = (e) => {
    e.preventDefault();
    const params = new URLSearchParams();
    if (searchParams.type) params.append('type', searchParams.type);
    if (searchParams.location) params.append('location', searchParams.location);
    if (searchParams.bedrooms) params.append('bedrooms', searchParams.bedrooms);
    window.location.href = `/properties?${params.toString()}`;
  };

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-gray-50">
        <div className="text-center">
          <div className="animate-spin rounded-full h-16 w-16 border-4 border-primary-200 border-t-primary-600 mx-auto mb-4"></div>
          <p className="text-gray-600 text-lg">جاري تحميل عقار زين...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Hero Section */}
      <section className="bg-gradient-to-r from-primary-900 to-primary-700 text-white py-20">
        <div className="container mx-auto px-4 text-center">
          <h1 className="text-5xl font-bold mb-4">عقار زين</h1>
          <p className="text-xl mb-8">شريكك العقاري في الإسماعيلية</p>
          
          {/* Search Form */}
          <form onSubmit={handleSearch} className="max-w-3xl mx-auto bg-white rounded-lg p-4 shadow-xl">
            <div className="grid grid-cols-1 md:grid-cols-4 gap-3">
              <select
                className="w-full p-3 border rounded-lg text-gray-900"
                value={searchParams.type}
                onChange={(e) => setSearchParams({...searchParams, type: e.target.value})}
              >
                <option value="">نوع العقار</option>
                <option value="sale">للبيع</option>
                <option value="rent">للإيجار</option>
              </select>
              
              <input
                type="text"
                placeholder="الموقع"
                className="w-full p-3 border rounded-lg text-gray-900"
                value={searchParams.location}
                onChange={(e) => setSearchParams({...searchParams, location: e.target.value})}
              />
              
              <select
                className="w-full p-3 border rounded-lg text-gray-900"
                value={searchParams.bedrooms}
                onChange={(e) => setSearchParams({...searchParams, bedrooms: e.target.value})}
              >
                <option value="">عدد الغرف</option>
                <option value="1">1 غرفة</option>
                <option value="2">2 غرف</option>
                <option value="3">3 غرف</option>
                <option value="4">4 غرف</option>
                <option value="5">5+ غرف</option>
              </select>
              
              <button type="submit" className="bg-primary-600 text-white p-3 rounded-lg hover:bg-primary-700 transition flex items-center justify-center gap-2">
                <FaSearch /> بحث
              </button>
            </div>
          </form>
        </div>
      </section>

      {/* إحصائيات */}
      <section className="container mx-auto px-4 -mt-10 relative z-10">
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
          {[
            { icon: FaHome, value: featured.length, label: 'عقار مميز', color: 'bg-blue-500' },
            { icon: FaFire, value: trending.length, label: 'الأكثر طلباً', color: 'bg-orange-500' },
            { icon: FaUsers, value: categories.length, label: 'تصنيف', color: 'bg-green-500' },
            { icon: FaHandshake, value: '50+', label: 'مسوق', color: 'bg-purple-500' }
          ].map((stat, i) => (
            <div key={i} className="bg-white rounded-lg shadow-lg p-4 text-center">
              <div className={`${stat.color} w-12 h-12 rounded-full flex items-center justify-center text-white text-xl mx-auto mb-2`}>
                <stat.icon />
              </div>
              <div className="text-2xl font-bold">{stat.value}</div>
              <div className="text-gray-600">{stat.label}</div>
            </div>
          ))}
        </div>
      </section>

      {/* التصنيفات */}
      <section className="py-16">
        <div className="container mx-auto px-4">
          <div className="flex justify-between items-center mb-8">
            <h2 className="text-3xl font-bold">التصنيفات</h2>
            <Link to="/categories" className="text-primary-600 hover:text-primary-700 flex items-center gap-2">
              <span>عرض الكل</span>
              <FaArrowLeft />
            </Link>
          </div>

          {categories.length === 0 ? (
            <div className="bg-yellow-50 border border-yellow-200 rounded-lg p-8 text-center">
              <p className="text-yellow-700">لا توجد تصنيفات متاحة</p>
            </div>
          ) : (
            <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
              {categories.slice(0, 6).map(category => (
                <CategoryCard key={category.id} category={category} />
              ))}
            </div>
          )}
        </div>
      </section>

      {/* العقارات المميزة */}
      <section className="py-16 bg-white">
        <div className="container mx-auto px-4">
          <div className="flex justify-between items-center mb-8">
            <h2 className="text-3xl font-bold">عقارات مميزة</h2>
            <Link to="/properties" className="text-primary-600 hover:text-primary-700 flex items-center gap-2">
              <span>عرض الكل</span>
              <FaArrowLeft />
            </Link>
          </div>

          {featured.length === 0 ? (
            <div className="bg-yellow-50 border border-yellow-200 rounded-lg p-8 text-center">
              <p className="text-yellow-700">لا توجد عقارات مميزة</p>
            </div>
          ) : (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {featured.slice(0, 3).map(property => (
                <PropertyCard key={property.id} property={property} />
              ))}
            </div>
          )}
        </div>
      </section>

      {/* الأكثر طلباً */}
      {trending.length > 0 && (
        <section className="py-16 bg-gray-50">
          <div className="container mx-auto px-4">
            <div className="flex items-center gap-3 mb-8">
              <div className="bg-orange-100 p-3 rounded-full">
                <FaFire className="text-orange-600 text-2xl" />
              </div>
              <h2 className="text-3xl font-bold">الأكثر طلباً</h2>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
              {trending.map(property => (
                <PropertyCard key={property.id} property={property} />
              ))}
            </div>
          </div>
        </section>
      )}

      {/* تنبؤات الأسعار */}
      {predictions && predictions.predictions && predictions.predictions.length > 0 && (
        <section className="py-16 bg-white">
          <div className="container mx-auto px-4">
            <div className="flex items-center gap-3 mb-8">
              <div className="bg-blue-100 p-3 rounded-full">
                <FaChartLine className="text-blue-600 text-2xl" />
              </div>
              <h2 className="text-3xl font-bold">تنبؤات الأسعار</h2>
            </div>

            <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
              {predictions.predictions.slice(0, 6).map((pred, i) => (
                <div key={i} className="bg-gray-50 rounded-lg p-3 text-center">
                  <div className="text-sm text-gray-600">{pred.month}</div>
                  <div className="font-bold text-primary-600">
                    {Math.round(pred.predicted_price / 1000)}ألف
                  </div>
                </div>
              ))}
            </div>
            
            <div className="mt-4 text-center text-gray-600">
              الاتجاه المتوقع: {
                predictions.trend === 'rising' ? '📈 ارتفاع' : 
                predictions.trend === 'falling' ? '📉 انخفاض' : '📊 استقرار'
              }
            </div>
          </div>
        </section>
      )}

      {/* معلومات المنصة */}
      <section className="py-16 bg-primary-50">
        <div className="container mx-auto px-4 text-center">
          <h2 className="text-3xl font-bold mb-4">عقار زين</h2>
          <p className="text-lg text-gray-700 max-w-2xl mx-auto">
            منصة عقارية رائدة في الإسماعيلية، نساعدك في العثور على منزل أحلامك بكل سهولة ويسر.
          </p>
          
          <div className="grid grid-cols-2 md:grid-cols-4 gap-6 mt-12">
            {[
              { icon: FaBrain, title: 'ذكاء اصطناعي', desc: 'توصيات ذكية' },
              { icon: FaChartLine, title: 'تحليلات', desc: 'تنبؤات دقيقة' },
              { icon: FaFire, title: 'ترند', desc: 'أحدث العقارات' },
              { icon: FaHandshake, title: 'ثقة', desc: 'خدمة موثوقة' }
            ].map((item, i) => (
              <div key={i} className="text-center">
                <div className="bg-white w-16 h-16 rounded-full flex items-center justify-center text-primary-600 text-2xl mx-auto mb-3 shadow">
                  <item.icon />
                </div>
                <h3 className="font-bold">{item.title}</h3>
                <p className="text-sm text-gray-600">{item.desc}</p>
              </div>
            ))}
          </div>
        </div>
      </section>
    </div>
  );
};

export default HomePage;
