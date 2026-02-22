import React, { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import { getCategory, getPropertiesByCategory } from '../services/api';
import PropertyCard from '../components/properties/PropertyCard';
import { FaArrowLeft, FaBuilding } from 'react-icons/fa';

const CategoryPropertiesPage = () => {
  const { slug } = useParams();
  const [category, setCategory] = useState(null);
  const [properties, setProperties] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchCategoryData();
  }, [slug]);

  const fetchCategoryData = async () => {
    setLoading(true);
    try {
      const [categoryRes, propertiesRes] = await Promise.all([
        getCategory(slug),
        getPropertiesByCategory(slug)
      ]);
      setCategory(categoryRes.data.data);
      setProperties(propertiesRes.data.data || []);
    } catch (error) {
      console.error('Error fetching category data:', error);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-gray-50 py-8">
        <div className="container mx-auto px-4">
          <div className="skeleton h-20 w-64 mb-8"></div>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {[1,2,3,4,5,6].map(i => <div key={i} className="skeleton h-80 rounded-xl"></div>)}
          </div>
        </div>
      </div>
    );
  }

  if (!category) {
    return (
      <div className="min-h-screen bg-gray-50 py-8">
        <div className="container mx-auto px-4 text-center">
          <h1 className="text-2xl font-bold mb-4">التصنيف غير موجود</h1>
          <Link to="/categories" className="btn-primary">العودة للتصنيفات</Link>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50 py-8">
      <div className="container mx-auto px-4">
        {/* Breadcrumb */}
        <div className="mb-6">
          <nav className="flex text-gray-500 text-sm">
            <Link to="/" className="hover:text-primary-600">الرئيسية</Link>
            <span className="mx-2">/</span>
            <Link to="/categories" className="hover:text-primary-600">التصنيفات</Link>
            <span className="mx-2">/</span>
            <span className="text-gray-900">{category.name}</span>
          </nav>
        </div>

        {/* Category Header */}
        <div className="bg-white rounded-2xl shadow-lg p-8 mb-8">
          <div className="flex items-center gap-4">
            <div className="bg-primary-100 p-4 rounded-2xl">
              <FaBuilding className="text-4xl text-primary-600" />
            </div>
            <div>
              <h1 className="text-3xl font-bold mb-2">{category.name}</h1>
              <p className="text-gray-600">{category.description}</p>
              <p className="text-primary-600 mt-2">{properties.length} عقار متاح</p>
            </div>
          </div>
        </div>

        {/* Properties Grid */}
        {properties.length > 0 ? (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {properties.map(property => (
              <PropertyCard key={property.id} property={property} />
            ))}
          </div>
        ) : (
          <div className="text-center py-12">
            <p className="text-gray-500 text-lg">لا توجد عقارات في هذا التصنيف</p>
          </div>
        )}
      </div>
    </div>
  );
};

export default CategoryPropertiesPage;
