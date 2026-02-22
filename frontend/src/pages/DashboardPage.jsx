import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { 
  FaHome, FaBuilding, FaUsers, FaCalendar, FaMoneyBill, FaStar,
  FaChartLine, FaCog, FaSignOutAlt, FaBell, FaSearch, FaPlus,
  FaEdit, FaTrash, FaEye, FaCheck, FaTimes
} from 'react-icons/fa';

const DashboardPage = () => {
  const { user, logout } = useAuth();
  const [activeTab, setActiveTab] = useState('overview');
  const [stats, setStats] = useState({
    properties: 150,
    users: 234,
    bookings: 45,
    revenue: 1250000
  });

  if (!user || user.role !== 'admin') {
    return (
      <div className="min-h-screen bg-gray-50 py-8">
        <div className="container mx-auto px-4">
          <div className="max-w-md mx-auto text-center">
            <FaBuilding className="text-6xl text-gray-300 mx-auto mb-4" />
            <h1 className="text-3xl font-bold mb-4">لوحة التحكم</h1>
            <p className="text-gray-600 mb-6">غير مصرح لك بالدخول إلى هذه الصفحة</p>
            <Link to="/" className="btn-primary inline-block">العودة للرئيسية</Link>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-100">
      <div className="flex">
        {/* Sidebar */}
        <div className="w-64 bg-white h-screen shadow-lg fixed">
          <div className="p-6">
            <h2 className="text-2xl font-bold text-primary-600 mb-6">عقار زين</h2>
            <nav className="space-y-2">
              <button
                onClick={() => setActiveTab('overview')}
                className={`w-full flex items-center gap-3 px-4 py-3 rounded-lg transition-colors ${
                  activeTab === 'overview' ? 'bg-primary-600 text-white' : 'hover:bg-gray-100'
                }`}
              >
                <FaChartLine />
                <span>نظرة عامة</span>
              </button>
              <button
                onClick={() => setActiveTab('properties')}
                className={`w-full flex items-center gap-3 px-4 py-3 rounded-lg transition-colors ${
                  activeTab === 'properties' ? 'bg-primary-600 text-white' : 'hover:bg-gray-100'
                }`}
              >
                <FaHome />
                <span>العقارات</span>
              </button>
              <button
                onClick={() => setActiveTab('users')}
                className={`w-full flex items-center gap-3 px-4 py-3 rounded-lg transition-colors ${
                  activeTab === 'users' ? 'bg-primary-600 text-white' : 'hover:bg-gray-100'
                }`}
              >
                <FaUsers />
                <span>المستخدمين</span>
              </button>
              <button
                onClick={() => setActiveTab('bookings')}
                className={`w-full flex items-center gap-3 px-4 py-3 rounded-lg transition-colors ${
                  activeTab === 'bookings' ? 'bg-primary-600 text-white' : 'hover:bg-gray-100'
                }`}
              >
                <FaCalendar />
                <span>الحجوزات</span>
              </button>
              <button
                onClick={() => setActiveTab('payments')}
                className={`w-full flex items-center gap-3 px-4 py-3 rounded-lg transition-colors ${
                  activeTab === 'payments' ? 'bg-primary-600 text-white' : 'hover:bg-gray-100'
                }`}
              >
                <FaMoneyBill />
                <span>المدفوعات</span>
              </button>
              <button
                onClick={() => setActiveTab('reviews')}
                className={`w-full flex items-center gap-3 px-4 py-3 rounded-lg transition-colors ${
                  activeTab === 'reviews' ? 'bg-primary-600 text-white' : 'hover:bg-gray-100'
                }`}
              >
                <FaStar />
                <span>التقييمات</span>
              </button>
              <button
                onClick={() => setActiveTab('settings')}
                className={`w-full flex items-center gap-3 px-4 py-3 rounded-lg transition-colors ${
                  activeTab === 'settings' ? 'bg-primary-600 text-white' : 'hover:bg-gray-100'
                }`}
              >
                <FaCog />
                <span>الإعدادات</span>
              </button>
            </nav>
          </div>
        </div>

        {/* Main Content */}
        <div className="flex-1 mr-64">
          {/* Header */}
          <header className="bg-white shadow-sm sticky top-0 z-10">
            <div className="flex justify-between items-center px-8 py-4">
              <h1 className="text-2xl font-bold">
                {activeTab === 'overview' && 'نظرة عامة'}
                {activeTab === 'properties' && 'إدارة العقارات'}
                {activeTab === 'users' && 'إدارة المستخدمين'}
                {activeTab === 'bookings' && 'إدارة الحجوزات'}
                {activeTab === 'payments' && 'المدفوعات'}
                {activeTab === 'reviews' && 'التقييمات'}
                {activeTab === 'settings' && 'الإعدادات'}
              </h1>
              <div className="flex items-center gap-4">
                <button className="p-2 hover:bg-gray-100 rounded-lg relative">
                  <FaBell />
                  <span className="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>
                <div className="flex items-center gap-3">
                  <img 
                    src={user.avatar || 'https://via.placeholder.com/40'} 
                    alt={user.name}
                    className="w-10 h-10 rounded-full object-cover"
                  />
                  <div>
                    <div className="font-semibold">{user.name}</div>
                    <div className="text-sm text-gray-500">مدير النظام</div>
                  </div>
                </div>
              </div>
            </div>
          </header>

          {/* Content */}
          <div className="p-8">
            {activeTab === 'overview' && (
              <div>
                {/* Statistics Cards */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                  <div className="bg-white rounded-2xl shadow-lg p-6">
                    <div className="flex items-center justify-between mb-4">
                      <div className="bg-blue-100 p-3 rounded-lg">
                        <FaHome className="text-blue-600 text-2xl" />
                      </div>
                      <span className="text-green-500 text-sm">+12%</span>
                    </div>
                    <div className="text-3xl font-bold mb-1">{stats.properties}</div>
                    <div className="text-gray-600">إجمالي العقارات</div>
                  </div>

                  <div className="bg-white rounded-2xl shadow-lg p-6">
                    <div className="flex items-center justify-between mb-4">
                      <div className="bg-green-100 p-3 rounded-lg">
                        <FaUsers className="text-green-600 text-2xl" />
                      </div>
                      <span className="text-green-500 text-sm">+8%</span>
                    </div>
                    <div className="text-3xl font-bold mb-1">{stats.users}</div>
                    <div className="text-gray-600">المستخدمين</div>
                  </div>

                  <div className="bg-white rounded-2xl shadow-lg p-6">
                    <div className="flex items-center justify-between mb-4">
                      <div className="bg-yellow-100 p-3 rounded-lg">
                        <FaCalendar className="text-yellow-600 text-2xl" />
                      </div>
                      <span className="text-red-500 text-sm">-3%</span>
                    </div>
                    <div className="text-3xl font-bold mb-1">{stats.bookings}</div>
                    <div className="text-gray-600">الحجوزات</div>
                  </div>

                  <div className="bg-white rounded-2xl shadow-lg p-6">
                    <div className="flex items-center justify-between mb-4">
                      <div className="bg-purple-100 p-3 rounded-lg">
                        <FaMoneyBill className="text-purple-600 text-2xl" />
                      </div>
                      <span className="text-green-500 text-sm">+25%</span>
                    </div>
                    <div className="text-3xl font-bold mb-1">{stats.revenue.toLocaleString()} ج.م</div>
                    <div className="text-gray-600">الإيرادات</div>
                  </div>
                </div>

                {/* Charts */}
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                  <div className="bg-white rounded-2xl shadow-lg p-6">
                    <h3 className="text-xl font-bold mb-4">نشاط العقارات</h3>
                    <div className="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                      <p className="text-gray-500">رسم بياني للعقارات</p>
                    </div>
                  </div>
                  <div className="bg-white rounded-2xl shadow-lg p-6">
                    <h3 className="text-xl font-bold mb-4">المبيعات الشهرية</h3>
                    <div className="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                      <p className="text-gray-500">رسم بياني للمبيعات</p>
                    </div>
                  </div>
                </div>

                {/* Recent Activity */}
                <div className="bg-white rounded-2xl shadow-lg p-6">
                  <h3 className="text-xl font-bold mb-4">آخر النشاطات</h3>
                  <div className="space-y-4">
                    {[1,2,3,4,5].map(i => (
                      <div key={i} className="flex items-center gap-4 p-3 hover:bg-gray-50 rounded-lg transition-colors">
                        <div className="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center">
                          {i % 3 === 0 && <FaHome className="text-primary-600" />}
                          {i % 3 === 1 && <FaUsers className="text-primary-600" />}
                          {i % 3 === 2 && <FaCalendar className="text-primary-600" />}
                        </div>
                        <div className="flex-1">
                          <div className="font-semibold">تم إضافة عقار جديد</div>
                          <div className="text-sm text-gray-500">منذ {i} ساعات</div>
                        </div>
                      </div>
                    ))}
                  </div>
                </div>
              </div>
            )}

            {activeTab === 'properties' && (
              <div className="bg-white rounded-2xl shadow-lg p-6">
                <div className="flex justify-between items-center mb-6">
                  <h3 className="text-xl font-bold">قائمة العقارات</h3>
                  <button className="btn-primary flex items-center gap-2">
                    <FaPlus /> إضافة عقار
                  </button>
                </div>
                
                <div className="mb-4 flex gap-4">
                  <div className="flex-1 relative">
                    <FaSearch className="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
                    <input
                      type="text"
                      placeholder="بحث عن عقار..."
                      className="input pr-10 w-full"
                    />
                  </div>
                </div>

                <div className="overflow-x-auto">
                  <table className="w-full">
                    <thead className="bg-gray-50">
                      <tr>
                        <th className="px-6 py-3 text-right">العقار</th>
                        <th className="px-6 py-3 text-right">النوع</th>
                        <th className="px-6 py-3 text-right">السعر</th>
                        <th className="px-6 py-3 text-right">الحالة</th>
                        <th className="px-6 py-3 text-right">الإجراءات</th>
                      </tr>
                    </thead>
                    <tbody>
                      {[1,2,3,4,5].map(i => (
                        <tr key={i} className="border-b hover:bg-gray-50">
                          <td className="px-6 py-4">
                            <div className="flex items-center gap-3">
                              <img 
                                src={`https://via.placeholder.com/50`} 
                                alt=""
                                className="w-12 h-12 rounded-lg object-cover"
                              />
                              <div>
                                <div className="font-semibold">فيلا فاخرة {i}</div>
                                <div className="text-sm text-gray-500">حي النخيل</div>
                              </div>
                            </div>
                          </td>
                          <td className="px-6 py-4">للبيع</td>
                          <td className="px-6 py-4">1,500,000 ج.م</td>
                          <td className="px-6 py-4">
                            <span className="badge badge-success">متاح</span>
                          </td>
                          <td className="px-6 py-4">
                            <div className="flex gap-2">
                              <button className="p-2 hover:bg-blue-50 rounded-lg text-blue-600">
                                <FaEye />
                              </button>
                              <button className="p-2 hover:bg-green-50 rounded-lg text-green-600">
                                <FaEdit />
                              </button>
                              <button className="p-2 hover:bg-red-50 rounded-lg text-red-600">
                                <FaTrash />
                              </button>
                            </div>
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              </div>
            )}

            {/* باقي التبويبات يمكن إضافتها بنفس النمط */}
          </div>
        </div>
      </div>
    </div>
  );
};

export default DashboardPage;
