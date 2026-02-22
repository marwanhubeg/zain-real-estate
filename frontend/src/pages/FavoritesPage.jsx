import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { getProperty } from '../services/api';
import PropertyCard from '../components/properties/PropertyCard';
import { FaHeart, FaArrowLeft } from 'react-icons/fa';

const FavoritesPage = () => {
  const [favorites, setFavorites] = useState([]);
  const [properties, setProperties] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    // استخدام localStorage مباشرة بدون التحقق من المستخدم
    const savedFavorites = JSON.parse(localStorage.getItem('favorites') || '[]');
    setFavorites(savedFavorites);
    fetchFavoriteProperties(savedFavorites);
  }, []);

  const fetchFavoriteProperties = async (slugs) => {
    setLoading(true);
    try {
      const promises = slugs.map(slug => getProperty(slug));
      const results = await Promise.all(promises);
      setProperties(results.map(res => res.data.data));
    } catch (error) {
      console.error('Error fetching favorite properties:', error);
    } finally {
      setLoading(false);
    }
  };

  const removeFromFavorites = (slug) => {
    const newFavorites = favorites.filter(s => s !== slug);
    setFavorites(newFavorites);
    localStorage.setItem('favorites', JSON.stringify(newFavorites));
    setProperties(properties.filter(p => p.slug !== slug));
  };

  return (
    <div className="min-h-screen bg-gray-50 py-8">
      <div className="container mx-auto px-4">
        {/* Header */}
        <div className="flex justify-between items-center mb-8">
          <div>
            <h1 className="text-3xl font-bold mb-2">المفضلة</h1>
            <p className="text-gray-600">عقاراتك المفضلة</p>
          </div>
          <Link to="/properties" className="text-primary-600 hover:text-primary-700 font-semibold flex items-center gap-2">
            <FaArrowLeft />
            <span>العودة للعقارات</span>
          </Link>
        </div>

        {loading ? (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {[1,2,3,4,5,6].map(i => (
              <div key={i} className="skeleton h-96 rounded-2xl"></div>
            ))}
          </div>
        ) : properties.length > 0 ? (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {properties.map(property => (
              <div key={property.id} className="relative">
                <PropertyCard property={property} />
                <button
                  onClick={() => removeFromFavorites(property.slug)}
                  className="absolute top-4 left-4 bg-red-500 text-white p-2 rounded-full hover:bg-red-600 transition-colors z-10"
                >
                  <FaHeart />
                </button>
              </div>
            ))}
          </div>
        ) : (
          <div className="text-center py-12">
            <FaHeart className="text-6xl text-gray-300 mx-auto mb-4" />
            <h3 className="text-2xl font-bold text-gray-400 mb-2">لا توجد عقارات في المفضلة</h3>
            <p className="text-gray-500 mb-6">أضف بعض العقارات إلى المفضلة</p>
            <Link to="/properties" className="btn-primary inline-block">
              تصفح العقارات
            </Link>
          </div>
        )}
      </div>
    </div>
  );
};

export default FavoritesPage;
