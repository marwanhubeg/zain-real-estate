import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { FaCalendar, FaClock, FaMapMarkerAlt, FaCheck, FaTimes, FaSpinner } from 'react-icons/fa';

const BookingsPage = () => {
  const [bookings, setBookings] = useState([]);
  const [loading, setLoading] = useState(true);
  const [filter, setFilter] = useState('all');

  useEffect(() => {
    fetchBookings();
  }, []);

  const fetchBookings = async () => {
    setLoading(true);
    try {
      // بيانات تجريبية
      const mockBookings = [
        {
          id: 1,
          property: {
            title: 'فيلا فاخرة في حي النخيل',
            slug: 'villa-1',
            location: 'حي النخيل',
            main_image: 'https://via.placeholder.com/400x300',
          },
          booking_date: '2026-03-15',
          booking_time: '10:00',
          type: 'visit',
          status: 'pending',
          notes: 'أود رؤية العقار في الصباح',
          created_at: '2026-02-20'
        },
        {
          id: 2,
          property: {
            title: 'شقة حديثة في الحي التجاري',
            slug: 'apartment-1',
            location: 'الحي التجاري',
            main_image: 'https://via.placeholder.com/400x300',
          },
          booking_date: '2026-03-10',
          booking_time: '14:00',
          type: 'visit',
          status: 'confirmed',
          notes: 'سأحضر مع العائلة',
          created_at: '2026-02-18'
        }
      ];
      setBookings(mockBookings);
    } catch (error) {
      console.error('Error fetching bookings:', error);
    } finally {
      setLoading(false);
    }
  };

  const getStatusBadge = (status) => {
    switch(status) {
      case 'pending':
        return <span className="badge badge-warning"><FaSpinner className="ml-1 animate-spin" /> قيد الانتظار</span>;
      case 'confirmed':
        return <span className="badge badge-success"><FaCheck className="ml-1" /> مؤكد</span>;
      case 'completed':
        return <span className="badge badge-primary"><FaCheck className="ml-1" /> مكتمل</span>;
      case 'cancelled':
        return <span className="badge badge-danger"><FaTimes className="ml-1" /> ملغي</span>;
      default:
        return null;
    }
  };

  const filteredBookings = filter === 'all' ? bookings : bookings.filter(b => b.status === filter);

  return (
    <div className="min-h-screen bg-gray-50 py-8">
      <div className="container mx-auto px-4">
        <div className="mb-8">
          <h1 className="text-3xl font-bold mb-2">حجوزاتي</h1>
          <p className="text-gray-600">إدارة جميع حجوزاتك ومواعيد المعاينة</p>
        </div>

        {/* Filter Tabs */}
        <div className="flex flex-wrap gap-2 mb-8">
          {['all', 'pending', 'confirmed', 'completed', 'cancelled'].map((status) => (
            <button
              key={status}
              onClick={() => setFilter(status)}
              className={`px-4 py-2 rounded-lg font-medium transition-all duration-300 ${
                filter === status
                  ? 'bg-primary-600 text-white'
                  : 'bg-white text-gray-600 hover:bg-gray-100'
              }`}
            >
              {status === 'all' && 'الكل'}
              {status === 'pending' && 'قيد الانتظار'}
              {status === 'confirmed' && 'مؤكدة'}
              {status === 'completed' && 'مكتملة'}
              {status === 'cancelled' && 'ملغية'}
            </button>
          ))}
        </div>

        {loading ? (
          <div className="space-y-4">
            {[1,2,3].map(i => <div key={i} className="skeleton h-32 rounded-2xl"></div>)}
          </div>
        ) : filteredBookings.length > 0 ? (
          <div className="space-y-4">
            {filteredBookings.map((booking) => (
              <div key={booking.id} className="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-shadow">
                <div className="flex flex-col md:flex-row gap-6">
                  <Link to={`/properties/${booking.property.slug}`} className="md:w-48 h-32 rounded-xl overflow-hidden">
                    <img src={booking.property.main_image} alt={booking.property.title} className="w-full h-full object-cover hover:scale-110 transition-transform duration-500" />
                  </Link>
                  <div className="flex-1">
                    <div className="flex flex-col md:flex-row md:items-center justify-between mb-4">
                      <Link to={`/properties/${booking.property.slug}`}>
                        <h3 className="text-xl font-bold hover:text-primary-600 transition-colors">{booking.property.title}</h3>
                      </Link>
                      {getStatusBadge(booking.status)}
                    </div>
                    <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                      <div className="flex items-center gap-2 text-gray-600"><FaCalendar className="text-primary-600" /><span>{booking.booking_date}</span></div>
                      <div className="flex items-center gap-2 text-gray-600"><FaClock className="text-primary-600" /><span>{booking.booking_time}</span></div>
                      <div className="flex items-center gap-2 text-gray-600"><FaMapMarkerAlt className="text-primary-600" /><span>{booking.property.location}</span></div>
                    </div>
                    {booking.notes && <p className="text-gray-600 text-sm bg-gray-50 p-3 rounded-lg"><span className="font-bold">ملاحظات:</span> {booking.notes}</p>}
                  </div>
                </div>
              </div>
            ))}
          </div>
        ) : (
          <div className="text-center py-12">
            <FaCalendar className="text-6xl text-gray-300 mx-auto mb-4" />
            <h3 className="text-2xl font-bold text-gray-400 mb-2">لا توجد حجوزات</h3>
            <p className="text-gray-500 mb-6">لم تقم بأي حجوزات بعد</p>
            <Link to="/properties" className="btn-primary inline-block">تصفح العقارات</Link>
          </div>
        )}
      </div>
    </div>
  );
};

export default BookingsPage;
