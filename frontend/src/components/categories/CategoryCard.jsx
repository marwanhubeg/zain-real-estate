import React from 'react';
import { Link } from 'react-router-dom';
import { FaBuilding, FaHome, FaStore, FaTree, FaTractor } from 'react-icons/fa';

const CategoryCard = ({ category }) => {
  const getIcon = (name) => {
    const icons = {
      'شقة': <FaBuilding className="text-3xl" />,
      'فيلا': <FaHome className="text-3xl" />,
      'منزل': <FaHome className="text-3xl" />,
      'محل تجاري': <FaStore className="text-3xl" />,
      'مكتب': <FaBuilding className="text-3xl" />,
      'أرض': <FaTree className="text-3xl" />,
      'مزرعة': <FaTractor className="text-3xl" />
    };
    return icons[category.name_ar] || <FaBuilding className="text-3xl" />;
  };

  return (
    <Link to={`/categories/${category.slug}`}>
      <div className="group relative bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-lg hover:shadow-2xl overflow-hidden transition-all duration-500 transform hover:-translate-y-2">
        <div className="absolute inset-0 bg-gradient-to-br from-primary-600 to-primary-800 opacity-0 group-hover:opacity-100 transition-all duration-500"></div>
        <div className="relative z-10 p-6 text-center">
          <div className="w-16 h-16 mx-auto mb-3 rounded-2xl bg-primary-100 group-hover:bg-white/20 flex items-center justify-center text-primary-600 group-hover:text-white transition-all duration-500">
            {getIcon(category.name_ar)}
          </div>
          <h3 className="font-bold text-lg mb-1 group-hover:text-white">{category.name_ar}</h3>
          <p className="text-gray-500 text-xs mb-2 group-hover:text-white/80">{category.name_en}</p>
          <span className="inline-block px-3 py-1 bg-primary-50 rounded-full text-xs group-hover:bg-white/20 group-hover:text-white">
            {category.properties_count || 0} عقار
          </span>
        </div>
      </div>
    </Link>
  );
};

export default CategoryCard;
