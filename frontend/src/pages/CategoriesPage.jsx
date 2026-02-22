import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { getCategories } from '../services/api';
import { FaBuilding, FaHome, FaStore, FaTree, FaTractor } from 'react-icons/fa';

const CategoriesPage = () => {
  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchCategories();
  }, []);

  const fetchCategories = async () => {
    setLoading(true);
    try {
      const res = await getCategories();
      setCategories(res.data.data || []);
    } catch (error) {
      console.error('Error fetching categories:', error);
    } finally {
      setLoading(false);
    }
  };

  // توزيع الأيقونات حسب التصنيف
  const getCategoryIcon = (name) => {
    const icons = {
      'شقة': <FaBuilding className="text-4xl text-primary-600" />,
      'فيلا': <FaHome className="text-4xl text-primary-600" />,
      'منزل': <FaHome className="text-4xl text-primary-600" />,
      'محل تجاري': <FaStore className="text-4xl text-primary-600" />,
      'مكتب': <FaBuilding className="text-4xl text-primary-600" />,
      'أرض': <FaTree className="text-4xl text-primary-600" />,
      'مزرعة': <FaTractor className="text-4xl text-primary-600" />,
    };
    return icons[name] || <FaBuilding className="text-4xl text-primary-600" />;
  };

  // تجميع التصنيفات المتشابهة
  const groupedCategories = categories.reduce((acc, cat) => {
    const key = cat.name_ar;
    if (!acc[key]) {
      acc[key] = {
        ...cat,
        count: 1,
        properties_total: cat.properties_count
      };
    } else {
      acc[key].count += 1;
      acc[key].properties_total += cat.properties_count;
    }
    return acc;
  }, {});

  const uniqueCategories = Object.values(groupedCategories);

  return (
    <div className="min-h-screen bg-gray-50 py-8">
      <div className="container mx-auto px-4">
        {/* Header */}
        <div className="text-center mb-12">
          <h1 className="text-4xl font-bold mb-4">تصنيفات العقارات</h1>
          <p className="text-gray-600 max-w-2xl mx-auto">
            تصفح العقارات حسب النوع الذي تبحث عنه. نقدم مجموعة متنوعة من التصنيفات لتسهيل عملية البحث
          </p>
        </div>

        {loading ? (
          <div className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
            {[1,2,3,4,5,6].map(i => (
              <div key={i} className="card animate-pulse p-6">
                <div className="w-16 h-16 bg-gray-300 rounded-full mx-auto mb-4"></div>
                <div className="h-4 bg-gray-300 rounded w-3/4 mx-auto mb-2"></div>
                <div className="h-4 bg-gray-300 rounded w-1/2 mx-auto"></div>
              </div>
            ))}
          </div>
        ) : (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            {uniqueCategories.map((category) => (
              <Link
                key={category.id}
                to={`/categories/${category.slug}`}
                className="card group hover:scale-105 transition duration-300"
              >
                <div className="p-8 text-center">
                  <div className="mb-4 flex justify-center">
                    {getCategoryIcon(category.name_ar)}
                  </div>
                  <h3 className="text-2xl font-bold mb-2">{category.name_ar}</h3>
                  <p className="text-gray-500 mb-2">{category.name_en}</p>
                  <div className="text-primary-600 font-semibold">
                    {category.properties_total || 0} عقار متاح
                  </div>
                  <div className="mt-4 text-primary-600 group-hover:translate-x-2 transition-transform">
                    عرض الكل ←
                  </div>
                </div>
              </Link>
            ))}
          </div>
        )}
      </div>
    </div>
  );
};

export default CategoriesPage;
