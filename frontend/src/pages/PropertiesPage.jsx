import React, { useState, useEffect } from 'react';
import { useSearchParams } from 'react-router-dom';
import { getProperties, getCategories, getLocations } from '../services/api';
import PropertyCard from '../components/properties/PropertyCard';
import { FaSearch, FaFilter } from 'react-icons/fa';
import toast from 'react-hot-toast';

const PropertiesPage = () => {
  const [searchParams, setSearchParams] = useSearchParams();
  const [properties, setProperties] = useState([]);
  const [categories, setCategories] = useState([]);
  const [locations, setLocations] = useState([]);
  const [loading, setLoading] = useState(true);
  const [meta, setMeta] = useState({});
  const [showFilters, setShowFilters] = useState(false);
  
  const [filters, setFilters] = useState({
    type: searchParams.get('type') || '',
    category_id: searchParams.get('category_id') || '',
    location: searchParams.get('location') || '',
    min_price: searchParams.get('min_price') || '',
    max_price: searchParams.get('max_price') || '',
    bedrooms: searchParams.get('bedrooms') || '',
    sort_by: searchParams.get('sort_by') || 'newest',
  });

  useEffect(() => {
    fetchCategories();
    fetchLocations();
    fetchProperties();
  }, []);

  const fetchCategories = async () => {
    try {
      const res = await getCategories();
      setCategories(res.data.data || []);
    } catch (error) {
      console.error('Error fetching categories:', error);
    }
  };

  const fetchLocations = async () => {
    try {
      const res = await getLocations();
      setLocations(res.data.data || []);
    } catch (error) {
      console.error('Error fetching locations:', error);
    }
  };

  const fetchProperties = async (page = 1) => {
    setLoading(true);
    try {
      const params = {
        ...filters,
        page,
        per_page: 12
      };
      // حذف الفلاتر الفارغة
      Object.keys(params).forEach(key => 
        params[key] === '' && delete params[key]
      );
      
      const res = await getProperties(params);
      setProperties(res.data.data || []);
      setMeta(res.data.meta || {});
    } catch (error) {
      toast.error('حدث خطأ في جلب العقارات');
      console.error('Error fetching properties:', error);
    } finally {
      setLoading(false);
    }
  };

  const handleSearch = (e) => {
    e.preventDefault();
    fetchProperties(1);
    // تحديث URL
    const params = new URLSearchParams();
    Object.keys(filters).forEach(key => {
      if (filters[key]) params.append(key, filters[key]);
    });
    setSearchParams(params);
  };

  const handleFilterChange = (e) => {
    const { name, value } = e.target;
    setFilters(prev => ({ ...prev, [name]: value }));
  };

  const handlePageChange = (newPage) => {
    fetchProperties(newPage);
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  const clearFilters = () => {
    setFilters({
      type: '',
      category_id: '',
      location: '',
      min_price: '',
      max_price: '',
      bedrooms: '',
      sort_by: 'newest',
    });
    setSearchParams({});
    fetchProperties(1);
  };

  return (
    <div className="min-h-screen bg-gray-50 py-8">
      <div className="container mx-auto px-4">
        {/* Header */}
        <div className="mb-8">
          <h1 className="text-3xl font-bold mb-2">عقارات للإيجار والبيع</h1>
          <p className="text-gray-600">تصفح أحدث العقارات المتاحة في الإسماعيلية</p>
        </div>

        {/* Mobile Filter Button */}
        <button
          onClick={() => setShowFilters(!showFilters)}
          className="md:hidden btn-outline w-full mb-4 flex items-center justify-center gap-2"
        >
          <FaFilter /> {showFilters ? 'إخفاء الفلاتر' : 'عرض الفلاتر'}
        </button>

        <div className="flex flex-col md:flex-row gap-6">
          {/* Filters Sidebar */}
          <div className={`${showFilters ? 'block' : 'hidden'} md:block md:w-1/4`}>
            <div className="bg-white rounded-lg shadow-md p-6 sticky top-20">
              <h2 className="text-xl font-bold mb-4">فلترة البحث</h2>
              
              <form onSubmit={handleSearch} className="space-y-4">
                {/* نوع العقار */}
                <div>
                  <label className="block text-gray-700 mb-2">نوع العقار</label>
                  <select
                    name="type"
                    value={filters.type}
                    onChange={handleFilterChange}
                    className="input w-full"
                  >
                    <option value="">الكل</option>
                    <option value="sale">للبيع</option>
                    <option value="rent">للإيجار</option>
                  </select>
                </div>

                {/* التصنيف */}
                <div>
                  <label className="block text-gray-700 mb-2">التصنيف</label>
                  <select
                    name="category_id"
                    value={filters.category_id}
                    onChange={handleFilterChange}
                    className="input w-full"
                  >
                    <option value="">الكل</option>
                    {categories.map(cat => (
                      <option key={cat.id} value={cat.id}>{cat.name}</option>
                    ))}
                  </select>
                </div>

                {/* الموقع */}
                <div>
                  <label className="block text-gray-700 mb-2">الموقع</label>
                  <select
                    name="location"
                    value={filters.location}
                    onChange={handleFilterChange}
                    className="input w-full"
                  >
                    <option value="">الكل</option>
                    {locations.map(loc => (
                      <option key={loc.id} value={loc.district}>{loc.district}</option>
                    ))}
                  </select>
                </div>

                {/* عدد الغرف */}
                <div>
                  <label className="block text-gray-700 mb-2">عدد الغرف</label>
                  <select
                    name="bedrooms"
                    value={filters.bedrooms}
                    onChange={handleFilterChange}
                    className="input w-full"
                  >
                    <option value="">الكل</option>
                    <option value="1">1 غرفة</option>
                    <option value="2">2 غرف</option>
                    <option value="3">3 غرف</option>
                    <option value="4">4 غرف</option>
                    <option value="5">5+ غرف</option>
                  </select>
                </div>

                {/* نطاق السعر */}
                <div className="grid grid-cols-2 gap-2">
                  <div>
                    <label className="block text-gray-700 mb-2">من</label>
                    <input
                      type="number"
                      name="min_price"
                      value={filters.min_price}
                      onChange={handleFilterChange}
                      placeholder="0"
                      className="input w-full"
                    />
                  </div>
                  <div>
                    <label className="block text-gray-700 mb-2">إلى</label>
                    <input
                      type="number"
                      name="max_price"
                      value={filters.max_price}
                      onChange={handleFilterChange}
                      placeholder="10,000,000"
                      className="input w-full"
                    />
                  </div>
                </div>

                {/* ترتيب */}
                <div>
                  <label className="block text-gray-700 mb-2">ترتيب حسب</label>
                  <select
                    name="sort_by"
                    value={filters.sort_by}
                    onChange={handleFilterChange}
                    className="input w-full"
                  >
                    <option value="newest">الأحدث</option>
                    <option value="price_asc">السعر: من الأقل للأعلى</option>
                    <option value="price_desc">السعر: من الأعلى للأقل</option>
                  </select>
                </div>

                {/* Buttons */}
                <div className="flex gap-2 pt-4">
                  <button type="submit" className="btn-primary flex-1 flex items-center justify-center gap-2">
                    <FaSearch /> بحث
                  </button>
                  <button 
                    type="button" 
                    onClick={clearFilters}
                    className="btn-outline px-4"
                  >
                    مسح
                  </button>
                </div>
              </form>
            </div>
          </div>

          {/* Properties Grid */}
          <div className="md:w-3/4">
            {/* Results Info */}
            <div className="bg-white rounded-lg shadow-md p-4 mb-6 flex justify-between items-center">
              <p>
                <span className="font-bold">{meta.total || 0}</span> عقار متاح
              </p>
              <p>الصفحة {meta.current_page || 1} من {meta.last_page || 1}</p>
            </div>

            {/* Loading State */}
            {loading ? (
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {[1,2,3,4,5,6].map(i => (
                  <div key={i} className="card animate-pulse">
                    <div className="h-48 bg-gray-300 rounded-t-lg"></div>
                    <div className="p-4 space-y-3">
                      <div className="h-4 bg-gray-300 rounded w-3/4"></div>
                      <div className="h-4 bg-gray-300 rounded w-1/2"></div>
                      <div className="h-4 bg-gray-300 rounded w-2/3"></div>
                    </div>
                  </div>
                ))}
              </div>
            ) : (
              <>
                {/* Properties Grid */}
                {properties.length > 0 ? (
                  <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {properties.map(property => (
                      <PropertyCard key={property.id} property={property} />
                    ))}
                  </div>
                ) : (
                  <div className="text-center py-12">
                    <p className="text-gray-500 text-lg">لا توجد عقارات متاحة</p>
                  </div>
                )}

                {/* Pagination */}
                {meta.last_page > 1 && (
                  <div className="flex justify-center gap-2 mt-8">
                    <button
                      onClick={() => handlePageChange(meta.current_page - 1)}
                      disabled={meta.current_page === 1}
                      className={`px-4 py-2 rounded-lg ${
                        meta.current_page === 1
                          ? 'bg-gray-200 text-gray-500 cursor-not-allowed'
                          : 'bg-primary-600 text-white hover:bg-primary-700'
                      }`}
                    >
                      السابق
                    </button>
                    
                    {[...Array(meta.last_page)].map((_, i) => (
                      <button
                        key={i}
                        onClick={() => handlePageChange(i + 1)}
                        className={`px-4 py-2 rounded-lg ${
                          meta.current_page === i + 1
                            ? 'bg-primary-600 text-white'
                            : 'bg-gray-200 hover:bg-gray-300'
                        }`}
                      >
                        {i + 1}
                      </button>
                    ))}
                    
                    <button
                      onClick={() => handlePageChange(meta.current_page + 1)}
                      disabled={meta.current_page === meta.last_page}
                      className={`px-4 py-2 rounded-lg ${
                        meta.current_page === meta.last_page
                          ? 'bg-gray-200 text-gray-500 cursor-not-allowed'
                          : 'bg-primary-600 text-white hover:bg-primary-700'
                      }`}
                    >
                      التالي
                    </button>
                  </div>
                )}
              </>
            )}
          </div>
        </div>
      </div>
    </div>
  );
};

export default PropertiesPage;
