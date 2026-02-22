import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { getLocations } from '../services/api';
import { FaMapMarkerAlt, FaBuilding, FaStar, FaArrowLeft } from 'react-icons/fa';

const LocationsPage = () => {
  const [locations, setLocations] = useState([]);
  const [loading, setLoading] = useState(true);
  const [selectedLocation, setSelectedLocation] = useState(null);

  useEffect(() => {
    fetchLocations();
  }, []);

  const fetchLocations = async () => {
    setLoading(true);
    try {
      const res = await getLocations();
      setLocations(res.data.data || []);
    } catch (error) {
      console.error('Error fetching locations:', error);
    } finally {
      setLoading(false);
    }
  };

  // تجميع المواقع حسب المدينة
  const groupedLocations = locations.reduce((acc, loc) => {
    const city = loc.city;
    if (!acc[city]) {
      acc[city] = [];
    }
    acc[city].push(loc);
    return acc;
  }, {});

  return (
    <div className="min-h-screen bg-gray-50 py-8">
      <div className="container mx-auto px-4">
        {/* Header */}
        <div className="text-center mb-12">
          <h1 className="text-4xl font-bold mb-4" data-aos="fade-down">المواقع العقارية</h1>
          <p className="text-gray-600 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="100">
            اكتشف أفضل المناطق والمواقع العقارية في الإسماعيلية
          </p>
        </div>

        {loading ? (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {[1,2,3,4,5,6].map(i => (
              <div key={i} className="skeleton h-48 rounded-2xl"></div>
            ))}
          </div>
        ) : (
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {/* قائمة المواقع */}
            <div className="lg:col-span-1 space-y-4">
              {Object.keys(groupedLocations).map((city, idx) => (
                <div key={city} className="bg-white rounded-2xl shadow-lg p-6" data-aos="fade-left" data-aos-delay={idx * 100}>
                  <h2 className="text-2xl font-bold mb-4 text-primary-600">{city}</h2>
                  <div className="space-y-3">
                    {groupedLocations[city].map((loc, index) => (
                      <button
                        key={loc.id}
                        onClick={() => setSelectedLocation(loc)}
                        className={`w-full text-right p-4 rounded-xl transition-all duration-300 ${
                          selectedLocation?.id === loc.id
                            ? 'bg-primary-50 border-2 border-primary-500'
                            : 'hover:bg-gray-50 border-2 border-transparent'
                        }`}
                      >
                        <div className="flex items-start gap-3">
                          <FaMapMarkerAlt className={`text-xl mt-1 ${
                            selectedLocation?.id === loc.id ? 'text-primary-600' : 'text-gray-400'
                          }`} />
                          <div className="flex-1">
                            <h3 className="font-semibold text-lg">{loc.district}</h3>
                            <p className="text-gray-500 text-sm">{loc.properties_count || 0} عقار متاح</p>
                          </div>
                        </div>
                      </button>
                    ))}
                  </div>
                </div>
              ))}
            </div>

            {/* تفاصيل الموقع */}
            <div className="lg:col-span-2">
              {selectedLocation ? (
                <div className="bg-white rounded-2xl shadow-lg p-6" data-aos="fade-right">
                  <h2 className="text-2xl font-bold mb-4">{selectedLocation.district}</h2>
                  
                  {/* خريطة OpenStreetMap بديلة */}
                  <div className="h-96 mb-6 rounded-xl overflow-hidden">
                    <iframe
                      title="location-map"
                      width="100%"
                      height="100%"
                      frameBorder="0"
                      src={`https://www.openstreetmap.org/export/embed.html?bbox=${selectedLocation.longitude ? selectedLocation.longitude-0.01 : 32.26}%2C${selectedLocation.latitude ? selectedLocation.latitude-0.01 : 30.58}%2C${selectedLocation.longitude ? selectedLocation.longitude+0.01 : 32.28}%2C${selectedLocation.latitude ? selectedLocation.latitude+0.01 : 30.62}&layer=mapnik&marker=${selectedLocation.latitude || 30.6046}%2C${selectedLocation.longitude || 32.2723}`}
                      style={{ border: 0 }}
                    ></iframe>
                  </div>

                  {/* معلومات الموقع */}
                  <div className="grid grid-cols-2 gap-4 mb-6">
                    <div className="p-4 bg-gray-50 rounded-xl">
                      <div className="text-2xl font-bold text-primary-600 mb-1">
                        {selectedLocation.properties_count || 0}
                      </div>
                      <div className="text-gray-600">عقار متاح</div>
                    </div>
                    <div className="p-4 bg-gray-50 rounded-xl">
                      <div className="text-2xl font-bold text-primary-600 mb-1">
                        {selectedLocation.latitude?.toFixed(4) || '30.6046'}°
                      </div>
                      <div className="text-gray-600">خط العرض</div>
                    </div>
                    <div className="p-4 bg-gray-50 rounded-xl">
                      <div className="text-2xl font-bold text-primary-600 mb-1">
                        {selectedLocation.longitude?.toFixed(4) || '32.2723'}°
                      </div>
                      <div className="text-gray-600">خط الطول</div>
                    </div>
                    <div className="p-4 bg-gray-50 rounded-xl">
                      <div className="text-2xl font-bold text-primary-600 mb-1">
                        ⭐ 4.5
                      </div>
                      <div className="text-gray-600">تقييم الموقع</div>
                    </div>
                  </div>

                  {/* أزرار الإجراءات */}
                  <div className="flex gap-4">
                    <Link
                      to={`/properties?location=${selectedLocation.district}`}
                      className="btn-primary flex-1 text-center"
                    >
                      عرض العقارات في هذا الموقع
                    </Link>
                    <a
                      href={`https://www.google.com/maps?q=${selectedLocation.latitude || 30.6046},${selectedLocation.longitude || 32.2723}`}
                      target="_blank"
                      rel="noopener noreferrer"
                      className="btn-outline flex-1 text-center"
                    >
                      فتح في Google Maps
                    </a>
                  </div>
                </div>
              ) : (
                <div className="bg-white rounded-2xl shadow-lg p-12 text-center">
                  <FaMapMarkerAlt className="text-6xl text-gray-300 mx-auto mb-4" />
                  <h3 className="text-2xl font-bold text-gray-400 mb-2">اختر موقعاً</h3>
                  <p className="text-gray-500">اختر أحد المواقع من القائمة لعرض التفاصيل على الخريطة</p>
                </div>
              )}
            </div>
          </div>
        )}
      </div>
    </div>
  );
};

export default LocationsPage;
