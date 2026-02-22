import React, { useState, useEffect } from 'react';
import { getPersonalizedRecommendations, getTrendingProperties, getRecommendationsByBudget } from '../../services/ai';
import PropertyCard from '../../components/properties/PropertyCard';
import { FaUser, FaFire, FaMoneyBill, FaArrowLeft } from 'react-icons/fa';
import { useAuth } from '../../context/AuthContext';
import { Link } from 'react-router-dom';

const RecommendationsPage = () => {
  const { user } = useAuth();
  const [activeTab, setActiveTab] = useState('personalized');
  const [recommendations, setRecommendations] = useState([]);
  const [loading, setLoading] = useState(true);
  const [budget, setBudget] = useState(500000);
  const [purpose, setPurpose] = useState('sale');

  useEffect(() => {
    fetchRecommendations();
  }, [activeTab, budget, purpose]);

  const fetchRecommendations = async () => {
    setLoading(true);
    try {
      let response;
      
      switch(activeTab) {
        case 'personalized':
          response = await getPersonalizedRecommendations(12);
          break;
        case 'trending':
          response = await getTrendingProperties(12);
          break;
        case 'budget':
          response = await getRecommendationsByBudget(budget, purpose, 12);
          break;
        default:
          response = await getTrendingProperties(12);
      }
      
      setRecommendations(response.data.data || []);
    } catch (error) {
      console.error('Error fetching recommendations:', error);
    } finally {
      setLoading(false);
    }
  };

  const tabs = [
    { id: 'personalized', label: 'توصيات مخصصة', icon: FaUser, color: 'purple' },
    { id: 'trending', label: 'الأكثر طلباً', icon: FaFire, color: 'orange' },
    { id: 'budget', label: 'حسب الميزانية', icon: FaMoneyBill, color: 'green' },
  ];

  return (
    <div className="min-h-screen bg-gray-50 py-8">
      <div className="container mx-auto px-4">
        {/* Header */}
        <div className="mb-8">
          <h1 className="text-3xl font-bold mb-2">توصيات ذكية</h1>
          <p className="text-gray-600">نظام ذكاء اصطناعي يوصيك بأفضل العقارات بناءً على تفضيلاتك</p>
        </div>

        {/* Tabs */}
        <div className="bg-white rounded-xl shadow-md p-2 mb-8 flex flex-wrap gap-2">
          {tabs.map((tab) => (
            <button
              key={tab.id}
              onClick={() => setActiveTab(tab.id)}
              className={`flex items-center gap-2 px-6 py-3 rounded-lg font-medium transition-all duration-300 ${
                activeTab === tab.id
                  ? `bg-${tab.color}-600 text-white`
                  : 'text-gray-600 hover:bg-gray-100'
              }`}
            >
              <tab.icon />
              <span>{tab.label}</span>
            </button>
          ))}
        </div>

        {/* Budget Filter (for budget tab) */}
        {activeTab === 'budget' && (
          <div className="bg-white rounded-xl shadow-md p-6 mb-8">
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label className="block text-gray-700 mb-2">الميزانية (جنيه)</label>
                <input
                  type="range"
                  min="100000"
                  max="5000000"
                  step="50000"
                  value={budget}
                  onChange={(e) => setBudget(parseInt(e.target.value))}
                  className="w-full"
                />
                <div className="text-center mt-2 font-bold text-primary-600">
                  {budget.toLocaleString()} ج.م
                </div>
              </div>
              <div>
                <label className="block text-gray-700 mb-2">الغرض</label>
                <select
                  value={purpose}
                  onChange={(e) => setPurpose(e.target.value)}
                  className="input w-full"
                >
                  <option value="sale">شراء</option>
                  <option value="rent">إيجار</option>
                </select>
              </div>
              <div className="flex items-end">
                <button
                  onClick={fetchRecommendations}
                  className="btn-primary w-full"
                >
                  تحديث التوصيات
                </button>
              </div>
            </div>
          </div>
        )}

        {/* Recommendations Grid */}
        {loading ? (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            {[...Array(8)].map((_, i) => (
              <div key={i} className="skeleton h-80 rounded-xl"></div>
            ))}
          </div>
        ) : recommendations.length > 0 ? (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            {recommendations.map((property, index) => (
              <div key={property.id} data-aos="fade-up" data-aos-delay={index * 50}>
                <PropertyCard property={property} />
              </div>
            ))}
          </div>
        ) : (
          <div className="text-center py-12">
            <p className="text-gray-500 text-lg">لا توجد توصيات متاحة</p>
          </div>
        )}
      </div>
    </div>
  );
};

export default RecommendationsPage;
