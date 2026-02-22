import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { getTrendingProperties, getPersonalizedRecommendations } from '../../services/ai';
import PropertyCard from '../properties/PropertyCard';
import { FaFire, FaUser, FaArrowLeft } from 'react-icons/fa';
import { useAuth } from '../../context/AuthContext';

const RecommendationsWidget = ({ limit = 4 }) => {
  const { user } = useAuth();
  const [recommendations, setRecommendations] = useState([]);
  const [loading, setLoading] = useState(true);
  const [type, setType] = useState('trending');

  useEffect(() => {
    fetchRecommendations();
  }, [user]);

  const fetchRecommendations = async () => {
    setLoading(true);
    try {
      let response;
      
      if (user) {
        response = await getPersonalizedRecommendations(limit);
        setType('personalized');
      } else {
        response = await getTrendingProperties(limit);
        setType('trending');
      }
      
      setRecommendations(response.data.data || []);
    } catch (error) {
      console.error('Error fetching recommendations:', error);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {[...Array(4)].map((_, i) => (
          <div key={i} className="skeleton h-64 rounded-xl"></div>
        ))}
      </div>
    );
  }

  if (recommendations.length === 0) return null;

  return (
    <div className="bg-white rounded-2xl shadow-lg p-6 mb-8">
      <div className="flex justify-between items-center mb-6">
        <div className="flex items-center gap-3">
          {type === 'personalized' ? (
            <>
              <div className="bg-purple-100 p-3 rounded-xl">
                <FaUser className="text-purple-600 text-xl" />
              </div>
              <div>
                <h2 className="text-2xl font-bold">توصيات مخصصة لك</h2>
                <p className="text-gray-600">بناءً على نشاطك وتفضيلاتك</p>
              </div>
            </>
          ) : (
            <>
              <div className="bg-orange-100 p-3 rounded-xl">
                <FaFire className="text-orange-600 text-xl" />
              </div>
              <div>
                <h2 className="text-2xl font-bold">الأكثر طلباً</h2>
                <p className="text-gray-600">أشهر العقارات في الإسماعيلية</p>
              </div>
            </>
          )}
        </div>
        <Link to={type === 'personalized' ? '/recommendations' : '/properties?sort=trending'} className="text-primary-600 hover:text-primary-700 font-semibold flex items-center gap-2">
          <span>عرض الكل</span>
          <FaArrowLeft />
        </Link>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {recommendations.map((property, index) => (
          <div key={property.id} data-aos="fade-up" data-aos-delay={index * 100}>
            <PropertyCard property={property} />
          </div>
        ))}
      </div>
    </div>
  );
};

export default RecommendationsWidget;
