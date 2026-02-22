import React from 'react';
import { Link } from 'react-router-dom';
import { 
  FaBed, 
  FaBath, 
  FaRulerCombined, 
  FaMapMarkerAlt, 
  FaHeart, 
  FaRegHeart,
  FaStar,
  FaShare
} from 'react-icons/fa';
import { useAuth } from '../../context/AuthContext';
import toast from 'react-hot-toast';

const PropertyCard = ({ property }) => {
  const { user } = useAuth();
  const [isFavorite, setIsFavorite] = React.useState(false);
  const [imageLoaded, setImageLoaded] = React.useState(false);

  React.useEffect(() => {
    // التحقق من المفضلة
    const favorites = JSON.parse(localStorage.getItem('favorites') || '[]');
    setIsFavorite(favorites.includes(property.slug));
  }, [property.slug]);

  const toggleFavorite = (e) => {
    e.preventDefault();
    e.stopPropagation();
    
    if (!user) {
      toast.error('يجب تسجيل الدخول أولاً');
      return;
    }
    
    const favorites = JSON.parse(localStorage.getItem('favorites') || '[]');
    let newFavorites;
    
    if (isFavorite) {
      newFavorites = favorites.filter(s => s !== property.slug);
      toast.success('تم إزالة العقار من المفضلة');
    } else {
      newFavorites = [...favorites, property.slug];
      toast.success('تم إضافة العقار إلى المفضلة');
    }
    
    localStorage.setItem('favorites', JSON.stringify(newFavorites));
    setIsFavorite(!isFavorite);
  };

  const handleShare = async (e) => {
    e.preventDefault();
    e.stopPropagation();
    
    if (navigator.share) {
      try {
        await navigator.share({
          title: property.title,
          text: property.description,
          url: window.location.origin + `/properties/${property.slug}`
        });
      } catch (error) {
        console.log('Error sharing:', error);
      }
    } else {
      navigator.clipboard.writeText(window.location.origin + `/properties/${property.slug}`);
      toast.success('تم نسخ الرابط');
    }
  };

  return (
    <div className="group relative bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
      {/* صورة العقار مع تأثيرات */}
      <Link to={`/properties/${property.slug}`} className="block relative overflow-hidden h-56">
        {!imageLoaded && (
          <div className="absolute inset-0 skeleton"></div>
        )}
        <img 
          src={property.main_image || 'https://via.placeholder.com/400x300'} 
          alt={property.title}
          className={`w-full h-full object-cover transition-all duration-700 group-hover:scale-110 ${imageLoaded ? 'opacity-100' : 'opacity-0'}`}
          onLoad={() => setImageLoaded(true)}
        />
        
        {/* شارة مميز */}
        {property.is_featured && (
          <div className="absolute top-4 right-4 bg-gradient-to-r from-yellow-500 to-yellow-600 text-white px-4 py-1.5 rounded-full text-sm font-semibold shadow-lg flex items-center gap-1 z-10">
            <FaStar className="text-xs" />
            <span>مميز</span>
          </div>
        )}
        
        {/* شارة النوع */}
        <div className={`absolute top-4 left-4 px-4 py-1.5 rounded-full text-sm font-semibold shadow-lg z-10 ${
          property.type === 'sale' 
            ? 'bg-gradient-to-r from-green-500 to-green-600' 
            : 'bg-gradient-to-r from-blue-500 to-blue-600'
        } text-white`}>
          {property.type_text}
        </div>
        
        {/* أزرار التفاعل */}
        <div className="absolute bottom-4 left-4 right-4 flex justify-between opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-4 group-hover:translate-y-0 z-20">
          <button
            onClick={toggleFavorite}
            className={`w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 shadow-lg ${
              isFavorite 
                ? 'bg-red-500 text-white hover:bg-red-600' 
                : 'bg-white text-gray-600 hover:bg-primary-600 hover:text-white'
            }`}
          >
            {isFavorite ? <FaHeart /> : <FaRegHeart />}
          </button>
          
          <button
            onClick={handleShare}
            className="w-10 h-10 rounded-full bg-white text-gray-600 hover:bg-primary-600 hover:text-white flex items-center justify-center transition-all duration-300 shadow-lg"
          >
            <FaShare />
          </button>
        </div>
        
        {/* Overlay داكن عند التمرير */}
        <div className="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
      </Link>

      {/* معلومات العقار */}
      <Link to={`/properties/${property.slug}`} className="block p-5">
        <h3 className="font-bold text-xl mb-2 line-clamp-1 hover:text-primary-600 transition-colors">
          {property.title}
        </h3>
        
        <div className="flex items-start gap-2 mb-4 text-gray-600">
          <FaMapMarkerAlt className="text-primary-600 mt-1 flex-shrink-0" />
          <span className="text-sm line-clamp-1">{property.location || property.address}</span>
        </div>
        
        {/* مواصفات العقار */}
        <div className="grid grid-cols-3 gap-3 mb-4">
          <div className="text-center p-2 bg-gray-50 rounded-lg group-hover:bg-primary-50 transition-colors">
            <FaBed className="text-primary-600 text-lg mx-auto mb-1" />
            <div className="font-semibold text-sm">{property.bedrooms}</div>
            <div className="text-xs text-gray-500">غرف</div>
          </div>
          
          <div className="text-center p-2 bg-gray-50 rounded-lg group-hover:bg-primary-50 transition-colors">
            <FaBath className="text-primary-600 text-lg mx-auto mb-1" />
            <div className="font-semibold text-sm">{property.bathrooms}</div>
            <div className="text-xs text-gray-500">حمامات</div>
          </div>
          
          <div className="text-center p-2 bg-gray-50 rounded-lg group-hover:bg-primary-50 transition-colors">
            <FaRulerCombined className="text-primary-600 text-lg mx-auto mb-1" />
            <div className="font-semibold text-sm">{property.area}</div>
            <div className="text-xs text-gray-500">م²</div>
          </div>
        </div>
        
        {/* السعر والمزيد */}
        <div className="flex items-center justify-between pt-3 border-t border-gray-100">
          <div>
            <span className="text-2xl font-bold text-primary-600">{property.formatted_price}</span>
          </div>
          
          <div className="flex items-center gap-1 text-primary-600 font-semibold group-hover:gap-3 transition-all">
            <span>تفاصيل</span>
            <span className="text-lg">←</span>
          </div>
        </div>
        
        {/* تقييم العقار */}
        {(property.reviews_avg_rating || property.reviews_count) && (
          <div className="flex items-center gap-2 mt-3 text-sm text-gray-600">
            <div className="flex items-center gap-1">
              <FaStar className="text-yellow-500" />
              <span className="font-semibold">{property.reviews_avg_rating || 0}</span>
            </div>
            <span>({property.reviews_count || 0} تقييم)</span>
          </div>
        )}
      </Link>
    </div>
  );
};

export default PropertyCard;
